/**
 * NewsBreak — Main Application JS
 * Handles: news fetching, infinite scroll, categories,
 *          dark mode, search, modal, trending sidebar
 */
'use strict';

const App = (() => {

  // ── Config ─────────────────────────────────────────────────
  const API_BASE = 'api/news.php';

  // State
  let state = {
    category:    'top',
    query:       '',
    page:        1,
    loading:     false,
    finished:    false,
    articles:    [],
    allArticles: [],   // buffer from current fetch
  };

  // DOM refs
  const $ = id => document.getElementById(id);
  const newsGrid      = $('newsGrid');
  const scrollLoader  = $('scrollLoader');
  const endOfFeed     = $('endOfFeed');
  const errorState    = $('errorState');
  const errorMsg      = $('errorMsg');
  const heroArticle   = $('heroArticle');
  const heroImg       = $('heroImg');
  const heroTitle     = $('heroTitle');
  const heroSource    = $('heroSource');
  const heroTime      = $('heroTime');
  const heroCatBadge  = $('heroCatBadge');
  const sectionTitle  = $('sectionTitle');
  const trendingList  = $('trendingList');
  const headlineList  = $('headlineList');
  const trendingWidget= $('trendingWidget');
  const loadTrigger   = $('loadMoreTrigger');
  const articleModal  = $('articleModal');
  const modalContent  = $('modalContent');
  const modalBackdrop = $('modalBackdrop');
  const modalClose    = $('modalClose');
  const searchBarWrap = $('searchBarWrap');
  const searchToggle  = $('searchToggle');
  const searchInput   = $('searchInput');
  const searchClear   = $('searchClear');
  const themeToggle   = $('themeToggle');
  const themeIcon     = $('themeIcon');
  const hamburger     = $('hamburger');
  const mobileDrawer  = $('mobileDrawer');
  const drawerOverlay = $('drawerOverlay');
  const drawerClose   = $('drawerClose');
  const navbar        = $('navbar');
  const toast         = $('toast');

  // Category labels
  const CAT_LABELS = {
    top: 'Top Stories', technology: 'Technology', business: 'Business',
    health: 'Health', entertainment: 'Entertainment', sports: 'Sports', science: 'Science'
  };
  const CAT_ICONS = {
    top: 'fa-fire-flame-curved', technology: 'fa-microchip', business: 'fa-briefcase',
    health: 'fa-heart-pulse', entertainment: 'fa-clapperboard', sports: 'fa-futbol', science: 'fa-flask'
  };

  // ── Init ────────────────────────────────────────────────────
  function init() {
    loadTheme();
    bindNav();
    bindSearch();
    bindTheme();
    bindModal();
    bindDrawer();
    bindScroll();
    fetchNews(true);
    fetchSidebars();
  }

  // ── Theme ────────────────────────────────────────────────────
  function loadTheme() {
    const saved = localStorage.getItem('nb_theme') || 'light';
    applyTheme(saved, false);
  }
  function applyTheme(theme, save = true) {
    document.documentElement.setAttribute('data-theme', theme);
    themeIcon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
    if (save) localStorage.setItem('nb_theme', theme);
  }
  function bindTheme() {
    themeToggle.addEventListener('click', () => {
      const cur = document.documentElement.getAttribute('data-theme');
      applyTheme(cur === 'dark' ? 'light' : 'dark');
    });
  }

  // ── Navbar scroll ────────────────────────────────────────────
  window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 10);
  }, { passive: true });

  // ── Category Nav ─────────────────────────────────────────────
  function bindNav() {
    document.querySelectorAll('.cat-btn, .drawer-cat, .topic-chip').forEach(btn => {
      btn.addEventListener('click', () => {
        const cat = btn.dataset.cat;
        if (!cat) return;
        setCategory(cat);
        if (mobileDrawer.classList.contains('open')) closeDrawer();
      });
    });
    document.querySelectorAll('.footer-links a[data-cat]').forEach(a => {
      a.addEventListener('click', e => { e.preventDefault(); setCategory(a.dataset.cat); });
    });
  }

  function setCategory(cat) {
    state.category = cat;
    state.query    = '';
    searchInput.value = '';
    searchClear.classList.remove('visible');
    // Update active states
    document.querySelectorAll('.cat-btn, .drawer-cat').forEach(b => {
      b.classList.toggle('active', b.dataset.cat === cat);
    });
    // Update section title
    sectionTitle.innerHTML = `<i class="fa-solid ${CAT_ICONS[cat] || 'fa-newspaper'}"></i> ${CAT_LABELS[cat] || cat}`;
    resetFeed();
    fetchNews(true);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  // ── Search ───────────────────────────────────────────────────
  let searchTimer;
  function bindSearch() {
    searchToggle.addEventListener('click', () => {
      searchBarWrap.classList.toggle('open');
      if (searchBarWrap.classList.contains('open')) {
        setTimeout(() => searchInput.focus(), 100);
      }
    });
    searchInput.addEventListener('input', () => {
      const v = searchInput.value.trim();
      searchClear.classList.toggle('visible', v.length > 0);
      clearTimeout(searchTimer);
      if (v.length < 2) { if (!v) { resetAndReload(); } return; }
      searchTimer = setTimeout(() => doSearch(v), 600);
    });
    searchInput.addEventListener('keydown', e => {
      if (e.key === 'Enter') { clearTimeout(searchTimer); doSearch(searchInput.value.trim()); }
      if (e.key === 'Escape') { closeSearch(); }
    });
    searchClear.addEventListener('click', () => {
      searchInput.value = ''; searchClear.classList.remove('visible');
      resetAndReload(); searchInput.focus();
    });
  }
  function doSearch(q) {
    if (!q) return;
    state.query = q;
    sectionTitle.innerHTML = `<i class="fa-solid fa-magnifying-glass"></i> Results for "${q}"`;
    resetFeed();
    fetchNews(true);
  }
  function closeSearch() {
    searchBarWrap.classList.remove('open');
  }
  function resetAndReload() {
    state.query = '';
    sectionTitle.innerHTML = `<i class="fa-solid ${CAT_ICONS[state.category]}"></i> ${CAT_LABELS[state.category]}`;
    resetFeed();
    fetchNews(true);
  }

  // ── Drawer ───────────────────────────────────────────────────
  function bindDrawer() {
    hamburger.addEventListener('click', openDrawer);
    drawerClose.addEventListener('click', closeDrawer);
    drawerOverlay.addEventListener('click', closeDrawer);
  }
  function openDrawer() {
    mobileDrawer.classList.add('open');
    drawerOverlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  }
  function closeDrawer() {
    mobileDrawer.classList.remove('open');
    drawerOverlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  // ── Modal ────────────────────────────────────────────────────
  function bindModal() {
    modalClose.addEventListener('click', closeModal);
    modalBackdrop.addEventListener('click', closeModal);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
  }
  function openModal(article) {
    const cat = CAT_LABELS[state.category] || 'News';
    const time = formatTime(article.publishedAt);

    let imgHtml = '';
    if (article.image) {
      imgHtml = `<img class="modal-img" src="${escapeHtml(article.image)}" alt="${escapeHtml(article.title)}" loading="lazy" onerror="this.style.display='none'">`;
    }

    // Clean content: remove [+xxx chars] suffix NewsAPI adds
    let body = article.content || article.description || 'No content available.';
    body = body.replace(/\[\+\d+ chars\]$/, '').trim();
    const paragraphs = body.split(/\n\n+/).filter(Boolean)
      .map(p => `<p>${escapeHtml(p)}</p>`).join('') || `<p>${escapeHtml(body)}</p>`;

    modalContent.innerHTML = `
      ${imgHtml}
      <div class="modal-category">${escapeHtml(cat)}</div>
      <h2 class="modal-title" id="modalTitle">${escapeHtml(article.title)}</h2>
      <div class="modal-byline">
        <span><i class="fa-solid fa-building-columns"></i> ${escapeHtml(article.source)}</span>
        <span class="dot-sep">·</span>
        <span><i class="fa-regular fa-clock"></i> ${time}</span>
        ${article.author ? `<span class="dot-sep">·</span><span><i class="fa-solid fa-user"></i> ${escapeHtml(article.author)}</span>` : ''}
      </div>
      <div class="modal-body">${paragraphs}</div>
      <div class="modal-ad">
        <span class="ad-label">Advertisement</span>
        <div class="ad-placeholder" style="min-height:100px;">
          <i class="fa-regular fa-rectangle-ad"></i><p>Your Ad Here</p><small>468×60</small>
        </div>
      </div>
      <a class="modal-read-more" href="${escapeHtml(article.url)}" target="_blank" rel="noopener noreferrer">
        <i class="fa-solid fa-arrow-up-right-from-square"></i> Read Full Article
      </a>
    `;
    articleModal.classList.add('open');
    document.body.style.overflow = 'hidden';
    modalContent.scrollTop = 0;
  }
  function closeModal() {
    articleModal.classList.remove('open');
    document.body.style.overflow = '';
  }

  // ── Infinite Scroll (IntersectionObserver) ───────────────────
  let observer;
  function bindScroll() {
    observer = new IntersectionObserver(entries => {
      if (entries[0].isIntersecting && !state.loading && !state.finished) {
        fetchNews(false);
      }
    }, { rootMargin: '200px' });
    if (loadTrigger) observer.observe(loadTrigger);
  }

  // ── Fetch News ───────────────────────────────────────────────
  async function fetchNews(reset = false) {
    if (state.loading) return;
    if (reset) { resetFeed(); showSkeletons(); }

    state.loading = true;
    scrollLoader.style.display = reset ? 'none' : 'flex';

    const params = new URLSearchParams({
      category: state.category,
      page:     state.page,
      pageSize: 12,
      ...(state.query ? { q: state.query } : {})
    });

    try {
      const res  = await fetch(`${API_BASE}?${params}`);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();

      if (data.status !== 'ok') throw new Error(data.message || 'API error');

      removeSkeletons();
      scrollLoader.style.display = 'none';

      const articles = (data.articles || []).filter(a => a.title);
      if (articles.length === 0) {
        if (state.page === 1) showError('No articles found. Try a different category or search.');
        else { state.finished = true; showEndOfFeed(); }
        state.loading = false;
        return;
      }

      // Hero on first load
      if (state.page === 1 && articles.length > 0 && !state.query) {
        const hero = articles.find(a => a.image) || articles[0];
        renderHero(hero);
      }

      renderArticles(articles, state.page === 1);
      state.articles = [...state.articles, ...articles];
      state.page++;

      // If fewer than pageSize returned, we're done
      if (articles.length < 12) { state.finished = true; showEndOfFeed(); }

      // Populate sidebars on first load
      if (state.page === 2) renderSidebars(articles);

    } catch (err) {
      removeSkeletons();
      scrollLoader.style.display = 'none';
      showError('Could not load news. Check your API key in api/news.php or try again.');
      console.error('NewsBreak fetch error:', err);
    }
    state.loading = false;
  }

  function resetFeed() {
    state.page     = 1;
    state.finished = false;
    state.articles = [];
    heroArticle.style.display = 'none';
    newsGrid.innerHTML = '';
    endOfFeed.style.display = 'none';
    errorState.style.display = 'none';
    scrollLoader.style.display = 'none';
  }

  // ── Skeletons ────────────────────────────────────────────────
  function showSkeletons() {
    newsGrid.innerHTML = Array(6).fill(0).map(() => `
      <div class="skeleton-card">
        <div class="skeleton-img skeleton"></div>
        <div class="skeleton-body">
          <div class="skeleton-line skeleton"></div>
          <div class="skeleton-line skeleton"></div>
          <div class="skeleton-line skeleton"></div>
          <div class="skeleton-line skeleton"></div>
        </div>
      </div>
    `).join('');
  }
  function removeSkeletons() {
    newsGrid.querySelectorAll('.skeleton-card').forEach(el => el.remove());
  }

  // ── Render Hero ──────────────────────────────────────────────
  function renderHero(article) {
    if (!article || !article.image) return;
    heroImg.src = article.image;
    heroImg.alt = article.title;
    heroImg.onerror = () => { heroArticle.style.display = 'none'; };
    heroTitle.textContent = article.title;
    heroSource.textContent = article.source;
    heroTime.textContent   = formatTime(article.publishedAt);
    heroCatBadge.textContent = CAT_LABELS[state.category] || 'News';
    heroArticle.style.display = 'block';
    heroArticle.onclick = () => openModal(article);
  }

  // ── Render Articles ──────────────────────────────────────────
  function renderArticles(articles, isFirst) {
    const fragment = document.createDocumentFragment();
    articles.forEach((article, i) => {
      const card = createCard(article, isFirst && i === 0 && !!article.image);
      fragment.appendChild(card);

      // Insert an inline ad every 8 cards
      if ((newsGrid.children.length + fragment.childNodes.length) % 8 === 0) {
        const adEl = document.createElement('div');
        adEl.className = 'ad-box ad-banner news-card';
        adEl.style.gridColumn = '1 / -1';
        adEl.innerHTML = `
          <span class="ad-label">Advertisement</span>
          <div class="ad-inner">
            <div class="ad-placeholder ad-banner-inner">
              <i class="fa-regular fa-rectangle-ad"></i>
              <p>Your Ad Here</p><small>728×90 Leaderboard</small>
            </div>
          </div>`;
        fragment.appendChild(adEl);
      }
    });
    newsGrid.appendChild(fragment);
  }

  function createCard(article, featured = false) {
    const el = document.createElement('div');
    el.className = 'news-card' + (featured ? ' featured' : '');
    el.setAttribute('tabindex', '0');
    el.setAttribute('role', 'article');
    el.setAttribute('aria-label', article.title);

    const imgHtml = article.image
      ? `<div class="card-img">
           <img src="${escapeHtml(article.image)}" alt="${escapeHtml(article.title)}" loading="lazy"
                onerror="this.parentElement.innerHTML='<div class=card-no-img><i class=\\'fa-regular fa-newspaper\\'></i></div>'">
           <span class="card-source-badge">${escapeHtml(article.source)}</span>
         </div>`
      : `<div class="card-img"><div class="card-no-img"><i class="fa-regular fa-newspaper"></i></div></div>`;

    el.innerHTML = `
      ${imgHtml}
      <div class="card-body">
        <div class="card-category">${escapeHtml(CAT_LABELS[state.category] || 'News')}</div>
        <h3 class="card-title">${escapeHtml(article.title)}</h3>
        ${article.description ? `<p class="card-desc">${escapeHtml(article.description)}</p>` : ''}
        <div class="card-footer">
          <div class="card-meta">
            <i class="fa-regular fa-clock"></i>
            ${formatTime(article.publishedAt)}
            <span class="dot-sep">·</span>
            ${escapeHtml(article.source)}
          </div>
          <button class="card-share-btn" title="Share" data-url="${escapeHtml(article.url)}" data-title="${escapeHtml(article.title)}">
            <i class="fa-solid fa-share-nodes"></i>
          </button>
        </div>
      </div>`;

    el.addEventListener('click', e => {
      if (e.target.closest('.card-share-btn')) { shareArticle(article); return; }
      openModal(article);
    });
    el.addEventListener('keydown', e => {
      if (e.key === 'Enter') openModal(article);
    });
    return el;
  }

  // ── Sidebars ─────────────────────────────────────────────────
  async function fetchSidebars() {
    // Trending: use tech + top
    try {
      const res  = await fetch(`${API_BASE}?category=top&pageSize=5&page=1`);
      const data = await res.json();
      if (data.status === 'ok' && data.articles.length) {
        renderTrending(data.articles.slice(0, 5));
        renderHeadlines(data.articles.slice(0, 6));
      }
    } catch (_) { /* sidebar failure is non-critical */ }
  }

  function renderSidebars(articles) {
    if (!trendingList.querySelector('.trending-item')) renderTrending(articles);
    if (!headlineList.querySelector('.headline-item'))   renderHeadlines(articles);
  }

  function renderTrending(articles) {
    trendingList.innerHTML = articles.map((a, i) => `
      <div class="trending-item" tabindex="0" data-idx="${i}">
        <span class="trending-rank">${i + 1}</span>
        <div>
          <div class="trending-text">${escapeHtml(a.title)}</div>
          <div class="trending-source">${escapeHtml(a.source)}</div>
        </div>
      </div>
    `).join('');
    trendingList.querySelectorAll('.trending-item').forEach((el, i) => {
      el.addEventListener('click', () => openModal(articles[i]));
      el.addEventListener('keydown', e => { if (e.key === 'Enter') openModal(articles[i]); });
    });
  }

  function renderHeadlines(articles) {
    headlineList.innerHTML = articles.map((a, i) => `
      <li class="headline-item" tabindex="0" data-idx="${i}">
        ${a.image
          ? `<img class="headline-thumb" src="${escapeHtml(a.image)}" alt="${escapeHtml(a.title)}" loading="lazy" onerror="this.style.display='none'">`
          : ''}
        <div class="headline-body">
          <div class="headline-title">${escapeHtml(a.title)}</div>
          <div class="headline-source">${escapeHtml(a.source)} · ${formatTime(a.publishedAt)}</div>
        </div>
      </li>
    `).join('');
    headlineList.querySelectorAll('.headline-item').forEach((el, i) => {
      el.addEventListener('click', () => openModal(articles[i]));
      el.addEventListener('keydown', e => { if (e.key === 'Enter') openModal(articles[i]); });
    });
  }

  // ── Helpers ──────────────────────────────────────────────────
  function showError(msg = '') {
    errorMsg.textContent = msg;
    errorState.style.display = 'block';
    heroArticle.style.display = 'none';
  }
  function showEndOfFeed() { endOfFeed.style.display = 'block'; }

  function formatTime(dateStr) {
    if (!dateStr) return '';
    try {
      const d    = new Date(dateStr);
      const diff = Math.floor((Date.now() - d) / 1000);
      if (diff < 60)       return 'Just now';
      if (diff < 3600)     return `${Math.floor(diff / 60)}m ago`;
      if (diff < 86400)    return `${Math.floor(diff / 3600)}h ago`;
      if (diff < 604800)   return `${Math.floor(diff / 86400)}d ago`;
      return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    } catch (_) { return ''; }
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function shareArticle(article) {
    if (navigator.share) {
      navigator.share({ title: article.title, url: article.url }).catch(() => {});
    } else {
      navigator.clipboard.writeText(article.url).then(() => showToast('Link copied!')).catch(() => {});
    }
  }

  let toastTimer;
  function showToast(msg) {
    toast.textContent = msg;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('show'), 2500);
  }

  // ── Public API ───────────────────────────────────────────────
  function retry() {
    errorState.style.display = 'none';
    state.finished = false;
    fetchNews(state.page === 1);
  }

  // Refresh button
  $('btnRefresh') && $('btnRefresh').addEventListener('click', () => {
    resetFeed(); fetchNews(true);
  });

  // ── Start ────────────────────────────────────────────────────
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }

  return { retry, setCategory, showToast };

})();
