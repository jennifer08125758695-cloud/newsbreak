# NewsBreak — Auto News Website
### Live news website with infinite scroll, dark mode, search & categories

---

## 📁 File Structure

```
newsbreak/
├── index.php               ← Main homepage
├── .htaccess               ← Apache config (gzip, caching, security)
├── css/
│   └── style.css           ← All styles
├── js/
│   └── app.js              ← All JavaScript (fetch, scroll, modal, etc.)
├── api/
│   └── news.php            ← PHP news proxy (NewsAPI / GNews / BBC RSS)
└── images/
    └── placeholder.svg     ← Fallback image
```

---

## 🚀 Setup on Hostinger (or any shared hosting)

### Step 1 — Upload Files
1. Log into your Hostinger control panel
2. Go to **File Manager** → your domain's `public_html` folder
3. Upload the entire **newsbreak** folder (or extract the ZIP directly there)
4. Make sure your domain points to the folder containing `index.php`

### Step 2 — Add Your API Key
Open **`api/news.php`** in the file manager or a text editor.

Find these lines near the top:
```php
define('NEWSAPI_KEY',  getenv('NEWSAPI_KEY')  ?: 'YOUR_NEWSAPI_KEY_HERE');
define('GNEWS_KEY',    getenv('GNEWS_KEY')    ?: 'YOUR_GNEWS_KEY_HERE');
```

Replace `YOUR_NEWSAPI_KEY_HERE` with your key.

### Step 3 — Get a Free API Key

#### Option A — NewsAPI.org (Recommended)
1. Go to https://newsapi.org/register
2. Sign up for free (100 requests/day on free tier)
3. Copy your API key
4. Paste it in `api/news.php` as shown above

#### Option B — GNews.io (Alternative)
1. Go to https://gnews.io/register
2. Free tier: 100 requests/day, 10 articles per request
3. Paste the key as `GNEWS_KEY`

#### Option C — No API Key (BBC RSS fallback)
The site works **without any API key** using BBC News RSS feeds.
News will be fetched automatically across all categories.
This is zero-cost and requires no registration.

### Step 4 — Set Cache Folder Permissions
The API creates an `api/cache/` folder automatically.
If you see permission errors, set the `api/` folder to **755** via Hostinger FTP/File Manager.

---

## ✅ Features Checklist

| Feature | Status |
|---|---|
| Infinite scroll (auto-load on scroll) | ✅ |
| 7 categories (Top, Tech, Business, Health, Entertainment, Sports, Science) | ✅ |
| Dark mode toggle | ✅ |
| Search bar with debounce | ✅ |
| Article modal (full content + source link) | ✅ |
| Hero featured article | ✅ |
| Trending sidebar | ✅ |
| Latest Headlines rail | ✅ |
| Skeleton loading animations | ✅ |
| Ad placeholders (AdSense-ready) | ✅ |
| Mobile responsive + hamburger drawer | ✅ |
| RSS fallback (no API key needed) | ✅ |
| PHP cache (300s TTL) | ✅ |
| Share button (Web Share API) | ✅ |
| Error handling + retry button | ✅ |
| SEO meta tags | ✅ |
| Lazy image loading | ✅ |
| Gzip + browser caching via .htaccess | ✅ |

---

## 💰 AdSense Integration

Replace the `<!-- Replace with AdSense -->` comment blocks in `index.php` with your AdSense code:

```html
<!-- Sidebar 300x250 -->
<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-XXXXXXXXXXXXXXXX"
     data-ad-slot="XXXXXXXXXX" data-ad-format="auto"></ins>
<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
```

Ad slots in the site:
- Left sidebar: 300×250
- Right sidebar: 300×600
- Feed banner (every 8 cards): 728×90 Leaderboard
- Article modal bottom: 468×60

---

## 🔧 Customization

### Change Site Name
Search for `NewsBreak` in `index.php` and replace with your brand.

### Change Logo Colors
In `css/style.css`, find:
```css
--accent: #e63946;
```
Replace with your brand color (hex code).

### Add More Categories
In `api/news.php`, add to `$categoryMap`:
```php
'politics' => 'general',
```
In `index.php`, add a button:
```html
<button class="cat-btn" data-cat="politics"><i class="fa-solid fa-landmark"></i> Politics</button>
```

---

## 🛠 Troubleshooting

| Problem | Solution |
|---|---|
| Blank page / no articles | Check API key in `api/news.php`. BBC RSS works without a key. |
| Mixed content warning | Make sure your site uses HTTPS. Check Hostinger SSL settings. |
| 403 on `api/news.php` | Check folder permissions. Set to 755. |
| CURL error | Enable cURL in Hostinger PHP settings, or use `allow_url_fopen` fallback. |
| Cache not clearing | Delete files in `api/cache/` folder manually. |

---

## 📄 License
Free to use for personal and commercial projects.
News content is sourced from third-party APIs and RSS feeds; all rights belong to original publishers.
