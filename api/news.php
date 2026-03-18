<?php
/**
 * NewsBreak - API Proxy
 * Fetches news from NewsAPI.org and GNews.io
 * Place your API key in config.php or directly below
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Cache-Control: public, max-age=300'); // 5 minute cache

// ============================================================
// CONFIGURATION — Add your API key here
// ============================================================
define('NEWSAPI_KEY',  getenv('NEWSAPI_KEY')  ?: 'YOUR_NEWSAPI_KEY_HERE');
define('GNEWS_KEY',    getenv('GNEWS_KEY')    ?: 'YOUR_GNEWS_KEY_HERE');
define('CACHE_DIR',    __DIR__ . '/cache/');
define('CACHE_TTL',    300); // seconds
// ============================================================

// Input sanitization
$category = isset($_GET['category']) ? preg_replace('/[^a-zA-Z]/', '', $_GET['category']) : 'general';
$query    = isset($_GET['q'])        ? htmlspecialchars(strip_tags($_GET['q']), ENT_QUOTES, 'UTF-8') : '';
$page     = isset($_GET['page'])     ? max(1, intval($_GET['page'])) : 1;
$pageSize = isset($_GET['pageSize']) ? min(20, max(5, intval($_GET['pageSize']))) : 12;

// Map our categories to NewsAPI categories
$categoryMap = [
    'top'           => 'general',
    'general'       => 'general',
    'technology'    => 'technology',
    'tech'          => 'technology',
    'business'      => 'business',
    'health'        => 'health',
    'entertainment' => 'entertainment',
    'sports'        => 'sports',
    'science'       => 'science',
];
$apiCategory = isset($categoryMap[$category]) ? $categoryMap[$category] : 'general';

// Create cache dir
if (!is_dir(CACHE_DIR)) {
    @mkdir(CACHE_DIR, 0755, true);
}

// Cache key
$cacheKey  = md5($apiCategory . $query . $page . $pageSize);
$cacheFile = CACHE_DIR . $cacheKey . '.json';

// Serve from cache if valid
if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < CACHE_TTL) {
    echo file_get_contents($cacheFile);
    exit;
}

// Try NewsAPI first, then GNews, then RSS fallback
$result = null;

if (NEWSAPI_KEY !== 'YOUR_NEWSAPI_KEY_HERE') {
    $result = fetchFromNewsAPI($apiCategory, $query, $page, $pageSize);
}

if (!$result && GNEWS_KEY !== 'YOUR_GNEWS_KEY_HERE') {
    $result = fetchFromGNews($apiCategory, $query, $page, $pageSize);
}

if (!$result) {
    $result = fetchFromRSS($apiCategory, $query);
}

if (!$result) {
    http_response_code(503);
    echo json_encode(['status' => 'error', 'message' => 'No news sources available. Please configure an API key.', 'articles' => []]);
    exit;
}

// Cache successful result
file_put_contents($cacheFile, json_encode($result));
echo json_encode($result);


// ============================================================
// NewsAPI.org
// ============================================================
function fetchFromNewsAPI($category, $query, $page, $pageSize) {
    if ($query) {
        $url = 'https://newsapi.org/v2/everything?'
             . 'q=' . urlencode($query . ' USA')
             . '&language=en'
             . '&sortBy=publishedAt'
             . '&page=' . $page
             . '&pageSize=' . $pageSize
             . '&apiKey=' . NEWSAPI_KEY;
    } else {
        $url = 'https://newsapi.org/v2/top-headlines?'
             . 'category=' . urlencode($category)
             . '&language=en'
             . '&country=us'
             . '&page=' . $page
             . '&pageSize=' . $pageSize
             . '&apiKey=' . NEWSAPI_KEY;
    }
    $raw = httpGet($url);
    if (!$raw) return null;
    $data = json_decode($raw, true);
    if (!$data || $data['status'] !== 'ok') return null;

    $articles = [];
    foreach ($data['articles'] as $a) {
        if (!$a['title'] || $a['title'] === '[Removed]') continue;
        $articles[] = [
            'title'       => $a['title'],
            'description' => $a['description'] ?? '',
            'content'     => $a['content'] ?? $a['description'] ?? '',
            'url'         => $a['url'],
            'image'       => $a['urlToImage'] ?? '',
            'source'      => $a['source']['name'] ?? 'Unknown',
            'publishedAt' => $a['publishedAt'] ?? '',
            'author'      => $a['author'] ?? '',
        ];
    }
    return ['status' => 'ok', 'totalResults' => $data['totalResults'] ?? count($articles), 'articles' => $articles, 'source' => 'newsapi'];
}

// ============================================================
// GNews.io (free: 100 req/day, 10 articles/req)
// ============================================================
function fetchFromGNews($category, $query, $page, $pageSize) {
    $gnewsCatMap = [
        'general'       => 'general',
        'technology'    => 'technology',
        'business'      => 'business',
        'health'        => 'health',
        'entertainment' => 'entertainment',
        'sports'        => 'sports',
        'science'       => 'science',
    ];
    $gnewsCat = $gnewsCatMap[$category] ?? 'general';

    if ($query) {
        $url = 'https://gnews.io/api/v4/search?'
             . 'q=' . urlencode($query)
             . '&lang=en&country=us'
             . '&max=' . $pageSize
             . '&page=' . $page
             . '&token=' . GNEWS_KEY;
    } else {
        $url = 'https://gnews.io/api/v4/top-headlines?'
             . 'category=' . $gnewsCat
             . '&lang=en&country=us'
             . '&max=' . $pageSize
             . '&page=' . $page
             . '&token=' . GNEWS_KEY;
    }
    $raw = httpGet($url);
    if (!$raw) return null;
    $data = json_decode($raw, true);
    if (!$data || empty($data['articles'])) return null;

    $articles = [];
    foreach ($data['articles'] as $a) {
        $articles[] = [
            'title'       => $a['title'] ?? '',
            'description' => $a['description'] ?? '',
            'content'     => $a['content'] ?? $a['description'] ?? '',
            'url'         => $a['url'],
            'image'       => $a['image'] ?? '',
            'source'      => $a['source']['name'] ?? 'GNews',
            'publishedAt' => $a['publishedAt'] ?? '',
            'author'      => $a['source']['name'] ?? '',
        ];
    }
    return ['status' => 'ok', 'totalResults' => $data['totalArticles'] ?? count($articles), 'articles' => $articles, 'source' => 'gnews'];
}

// ============================================================
// RSS Fallback (no API key needed)
// ============================================================
function fetchFromRSS($category, $query) {
    $rssFeeds = [
        'general'       => 'https://feeds.npr.org/1001/rss.xml',
        'technology'    => 'https://feeds.npr.org/1019/rss.xml',
        'business'      => 'https://feeds.npr.org/1006/rss.xml',
        'health'        => 'https://feeds.npr.org/1128/rss.xml',
        'entertainment' => 'https://feeds.npr.org/1008/rss.xml',
        'sports'        => 'https://feeds.npr.org/1055/rss.xml',
        'science'       => 'https://feeds.npr.org/1007/rss.xml',
    ];

    $feedUrl = $rssFeeds[$category] ?? $rssFeeds['general'];
    $raw = httpGet($feedUrl);
    if (!$raw) return null;

    libxml_use_internal_errors(true);
    $xml = simplexml_load_string($raw);
    if (!$xml) return null;

    $articles = [];
    $items = $xml->channel->item ?? [];

    foreach ($items as $item) {
        $title = (string)$item->title;
        if ($query && stripos($title, $query) === false) continue;

        // Extract image from media:thumbnail or enclosure
        $image = '';
        $ns = $item->getNameSpaces(true);
        if (isset($ns['media'])) {
            $media = $item->children($ns['media']);
            if (isset($media->thumbnail)) {
                $attrs = $media->thumbnail->attributes();
                $image = (string)$attrs['url'];
            }
        }
        if (!$image && isset($item->enclosure)) {
            $enc = $item->enclosure->attributes();
            if (strpos((string)$enc['type'], 'image') !== false) {
                $image = (string)$enc['url'];
            }
        }

        $articles[] = [
            'title'       => $title,
            'description' => strip_tags((string)$item->description),
            'content'     => strip_tags((string)$item->description),
            'url'         => (string)$item->link,
            'image'       => $image,
            'source'      => 'NPR News',
            'publishedAt' => (string)$item->pubDate,
            'author'      => 'NPR News',
        ];

        if (count($articles) >= 20) break;
    }

    if (empty($articles)) return null;
    return ['status' => 'ok', 'totalResults' => count($articles), 'articles' => $articles, 'source' => 'rss'];
}

// ============================================================
// HTTP GET helper
// ============================================================
function httpGet($url) {
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_USERAGENT      => 'NewsBreak/1.0',
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => true,
        ]);
        $response = curl_exec($ch);
        $err      = curl_error($ch);
        curl_close($ch);
        if ($err || !$response) return null;
        return $response;
    } elseif (ini_get('allow_url_fopen')) {
        $ctx = stream_context_create(['http' => ['timeout' => 10, 'user_agent' => 'NewsBreak/1.0']]);
        return @file_get_contents($url, false, $ctx) ?: null;
    }
    return null;
}
