<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="NewsBreak - Your daily source for breaking news, technology, business, health, and more.">
  <meta name="keywords" content="news, breaking news, technology, business, health, sports, entertainment">
  <meta property="og:title" content="NewsBreak - Live News Feed">
  <meta property="og:description" content="Stay updated with the latest breaking news from around the world.">
  <meta property="og:type" content="website">
  <meta name="theme-color" content="#0f172a">
  <title>NewsBreak — Live News Feed</title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Styles -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- ===================== NAVBAR ===================== -->
<header class="navbar" id="navbar">
  <div class="navbar-inner">
    <div class="navbar-left">
      <button class="hamburger" id="hamburger" aria-label="Menu">
        <span></span><span></span><span></span>
      </button>
      <a href="index.php" class="logo">
        <span class="logo-icon"><i class="fa-solid fa-bolt"></i></span>
        <span class="logo-text">NewsBreak</span>
      </a>
    </div>

    <nav class="category-nav" id="categoryNav">
      <button class="cat-btn active" data-cat="top"><i class="fa-solid fa-fire-flame-curved"></i> Top</button>
      <button class="cat-btn" data-cat="technology"><i class="fa-solid fa-microchip"></i> Tech</button>
      <button class="cat-btn" data-cat="business"><i class="fa-solid fa-briefcase"></i> Business</button>
      <button class="cat-btn" data-cat="health"><i class="fa-solid fa-heart-pulse"></i> Health</button>
      <button class="cat-btn" data-cat="entertainment"><i class="fa-solid fa-clapperboard"></i> Entertainment</button>
      <button class="cat-btn" data-cat="sports"><i class="fa-solid fa-futbol"></i> Sports</button>
      <button class="cat-btn" data-cat="science"><i class="fa-solid fa-flask"></i> Science</button>
    </nav>

    <div class="navbar-right">
      <button class="search-toggle" id="searchToggle" aria-label="Search">
        <i class="fa-solid fa-magnifying-glass"></i>
      </button>
      <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">
        <i class="fa-solid fa-moon" id="themeIcon"></i>
      </button>
    </div>
  </div>

  <!-- Search Bar -->
  <div class="search-bar-wrap" id="searchBarWrap">
    <div class="search-bar-inner">
      <i class="fa-solid fa-magnifying-glass search-ico"></i>
      <input type="text" id="searchInput" placeholder="Search news, topics, sources…" autocomplete="off">
      <button class="search-clear" id="searchClear" aria-label="Clear search">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
  </div>
</header>

<!-- Mobile Category Drawer -->
<div class="mobile-drawer" id="mobileDrawer">
  <div class="drawer-header">
    <span class="logo-text"><i class="fa-solid fa-bolt"></i> NewsBreak</span>
    <button class="drawer-close" id="drawerClose"><i class="fa-solid fa-xmark"></i></button>
  </div>
  <nav class="drawer-nav">
    <button class="drawer-cat active" data-cat="top"><i class="fa-solid fa-fire-flame-curved"></i> Top News</button>
    <button class="drawer-cat" data-cat="technology"><i class="fa-solid fa-microchip"></i> Technology</button>
    <button class="drawer-cat" data-cat="business"><i class="fa-solid fa-briefcase"></i> Business</button>
    <button class="drawer-cat" data-cat="health"><i class="fa-solid fa-heart-pulse"></i> Health</button>
    <button class="drawer-cat" data-cat="entertainment"><i class="fa-solid fa-clapperboard"></i> Entertainment</button>
    <button class="drawer-cat" data-cat="sports"><i class="fa-solid fa-futbol"></i> Sports</button>
    <button class="drawer-cat" data-cat="science"><i class="fa-solid fa-flask"></i> Science</button>
  </nav>
</div>
<div class="drawer-overlay" id="drawerOverlay"></div>

<!-- ===================== MAIN ===================== -->
<main class="main-layout">

  <!-- ── LEFT COLUMN (desktop sidebar) ── -->
  <aside class="sidebar">
    <!-- Trending -->
    <div class="widget" id="trendingWidget">
      <div class="widget-title"><i class="fa-solid fa-arrow-trend-up"></i> Trending Now</div>
      <div class="trending-list" id="trendingList">
        <div class="skeleton-trending"></div>
        <div class="skeleton-trending"></div>
        <div class="skeleton-trending"></div>
        <div class="skeleton-trending"></div>
        <div class="skeleton-trending"></div>
      </div>
    </div>

    <!-- Ad Placeholder -->
    <div class="ad-box ad-sidebar">
      <span class="ad-label">Advertisement</span>
      <div class="ad-inner">
        <!-- Replace this div with your Google AdSense or banner code -->
        <div class="ad-placeholder">
          <i class="fa-regular fa-rectangle-ad"></i>
          <p>Your Ad Here</p>
          <small>300×250</small>
        </div>
      </div>
    </div>

    <!-- Categories Quick Links -->
    <div class="widget">
      <div class="widget-title"><i class="fa-solid fa-grid-2"></i> Browse Topics</div>
      <div class="topic-grid">
        <button class="topic-chip" data-cat="technology"><i class="fa-solid fa-microchip"></i> Tech</button>
        <button class="topic-chip" data-cat="business"><i class="fa-solid fa-briefcase"></i> Business</button>
        <button class="topic-chip" data-cat="health"><i class="fa-solid fa-heart-pulse"></i> Health</button>
        <button class="topic-chip" data-cat="entertainment"><i class="fa-solid fa-clapperboard"></i> Entertainment</button>
        <button class="topic-chip" data-cat="sports"><i class="fa-solid fa-futbol"></i> Sports</button>
        <button class="topic-chip" data-cat="science"><i class="fa-solid fa-flask"></i> Science</button>
      </div>
    </div>
  </aside>

  <!-- ── CENTER COLUMN (main feed) ── -->
  <section class="feed-column">

    <!-- Hero Featured Article -->
    <div class="hero-article" id="heroArticle" style="display:none;">
      <div class="hero-img-wrap">
        <img id="heroImg" src="" alt="" loading="lazy">
        <div class="hero-overlay"></div>
        <div class="hero-meta">
          <span class="hero-category-badge" id="heroCatBadge">Top</span>
          <h1 class="hero-title" id="heroTitle"></h1>
          <div class="hero-info">
            <span id="heroSource"></span>
            <span class="dot-sep">·</span>
            <span id="heroTime"></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Section Header -->
    <div class="section-header">
      <h2 class="section-title" id="sectionTitle"><i class="fa-solid fa-fire-flame-curved"></i> Top Stories</h2>
      <span class="live-badge"><i class="fa-solid fa-circle"></i> LIVE</span>
    </div>

    <!-- Ad Banner (inline) -->
    <div class="ad-box ad-banner">
      <span class="ad-label">Advertisement</span>
      <div class="ad-inner">
        <!-- Replace with AdSense 728×90 leaderboard -->
        <div class="ad-placeholder ad-banner-inner">
          <i class="fa-regular fa-rectangle-ad"></i>
          <p>Your Banner Ad Here</p>
          <small>728×90 Leaderboard</small>
        </div>
      </div>
    </div>

    <!-- News Grid -->
    <div class="news-grid" id="newsGrid">
      <!-- Skeleton loaders injected by JS -->
    </div>

    <!-- Infinite Scroll Trigger + Loader -->
    <div class="load-more-trigger" id="loadMoreTrigger"></div>
    <div class="scroll-loader" id="scrollLoader" style="display:none;">
      <div class="pulse-ring"></div>
      <div class="pulse-ring pulse-ring--2"></div>
      <div class="pulse-ring pulse-ring--3"></div>
    </div>

    <!-- End of Feed -->
    <div class="end-of-feed" id="endOfFeed" style="display:none;">
      <i class="fa-solid fa-check-circle"></i>
      <p>You're all caught up!</p>
      <button class="btn-refresh" id="btnRefresh">Refresh Feed</button>
    </div>

    <!-- Error State -->
    <div class="error-state" id="errorState" style="display:none;">
      <i class="fa-solid fa-triangle-exclamation"></i>
      <h3>Couldn't load news</h3>
      <p id="errorMsg">Please check your API key or internet connection.</p>
      <button class="btn-refresh" onclick="App.retry()">Try Again</button>
    </div>
  </section>

  <!-- ── RIGHT COLUMN ── -->
  <aside class="sidebar sidebar-right">
    <!-- Ad Box -->
    <div class="ad-box ad-sidebar">
      <span class="ad-label">Advertisement</span>
      <div class="ad-inner">
        <!-- Replace with AdSense 300×600 -->
        <div class="ad-placeholder">
          <i class="fa-regular fa-rectangle-ad"></i>
          <p>Your Ad Here</p>
          <small>300×600</small>
        </div>
      </div>
    </div>

    <!-- Latest Headlines (right rail) -->
    <div class="widget" id="headlinesWidget">
      <div class="widget-title"><i class="fa-solid fa-newspaper"></i> Latest Headlines</div>
      <ul class="headline-list" id="headlineList">
        <li class="skeleton-headline"></li>
        <li class="skeleton-headline"></li>
        <li class="skeleton-headline"></li>
        <li class="skeleton-headline"></li>
        <li class="skeleton-headline"></li>
      </ul>
    </div>
  </aside>

</main>

<!-- ===================== FOOTER ===================== -->
<footer class="footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <span class="logo-text"><i class="fa-solid fa-bolt"></i> NewsBreak</span>
      <p>Your daily digest of breaking news from around the world.</p>
    </div>
    <div class="footer-links">
      <strong>Categories</strong>
      <a href="#" data-cat="top">Top News</a>
      <a href="#" data-cat="technology">Technology</a>
      <a href="#" data-cat="business">Business</a>
      <a href="#" data-cat="health">Health</a>
      <a href="#" data-cat="sports">Sports</a>
      <a href="#" data-cat="science">Science</a>
    </div>
    <div class="footer-links">
      <strong>Follow</strong>
      <a href="#"><i class="fa-brands fa-x-twitter"></i> Twitter</a>
      <a href="#"><i class="fa-brands fa-facebook"></i> Facebook</a>
      <a href="#"><i class="fa-brands fa-instagram"></i> Instagram</a>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© <?php echo date('Y'); ?> NewsBreak. USA news powered by <a href="https://newsapi.org" target="_blank" rel="noopener">NewsAPI</a> &amp; <a href="https://npr.org" target="_blank" rel="noopener">NPR News</a>.</p>
  </div>
</footer>

<!-- Article Detail Modal -->
<div class="article-modal" id="articleModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal-backdrop" id="modalBackdrop"></div>
  <div class="modal-box">
    <div class="modal-header">
      <button class="modal-close" id="modalClose" aria-label="Close"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="modal-content" id="modalContent">
      <!-- Populated by JS -->
    </div>
  </div>
</div>

<!-- Toast notification -->
<div class="toast" id="toast"></div>

<!-- JS -->
<script src="js/app.js"></script>
</body>
</html>
