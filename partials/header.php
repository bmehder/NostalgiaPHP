<header>
  <div class="outer">
    <div class="inner spread-apart" data-inner-header>
      <a class="brand" href="<?= url('/') ?>"><?= htmlspecialchars(site('name')) ?></a>

      <!-- add this button -->
      <button class="nav-toggle" data-nav-toggle aria-expanded="false" aria-controls="site-nav">
        <span class="visually-hidden">Menu</span>
        <svg viewBox="0 0 24 24" width="40" height="40" fill="white" aria-hidden="true">
          <rect x="3" y="6" width="18" height="2" rx="1"></rect>
          <rect x="3" y="11" width="18" height="2" rx="1"></rect>
          <rect x="3" y="16" width="18" height="2" rx="1"></rect>
        </svg>
      </button>

      <nav id="site-nav" data-site-nav class="nav spread-apart">
        <?= nav_link('/', 'Home', $path) ?>
        <?= nav_link('/about', 'About', $path) ?>
        <?= nav_link('/blog', 'Blog', $path) ?>
        <?= nav_link('/tags', 'Tags', $path) ?>
        <?= nav_link('/dox', 'Dox', $path) ?>
        <?= nav_link('/contact', 'Contact', $path) ?>
        <?= nav_link('/tools/admin.php', 'Admin', $path) ?>
        <?= nav_link('/sitemap.xml', 'Sitemap', $path) ?>
        <a href="https://github.com/bmehder/NostalgiaPHP" class="github-link" target="_blank" rel="noopener">
          <svg viewBox="0 0 16 16" width="20" height="20" aria-hidden="true">
            <path fill="currentColor"
              d="M8 .2a8 8 0 0 0-2.5 15.6c.4.1.6-.2.6-.4v-1.4c-2.5.5-3-1.2-3-1.2-.3-.9-.8-1.2-.8-1.2-.7-.5.1-.5.1-.5.8.1 1.2.8 1.2.8.7 1.2 1.9.9 2.4.7.1-.5.3-.9.5-1.1-2-.2-4.1-1-4.1-4.3 0-1 .4-1.9 1-2.6-.1-.2-.4-1.1.1-2.3 0 0 .8-.2 2.6 1a9 9 0 0 1 4.7 0c1.8-1.2 2.6-1 2.6-1 .5 1.2.2 2.1.1 2.3.7.7 1 1.6 1 2.6 0 3.3-2.1 4-4.1 4.3.3.2.5.6.5 1.3v1.9c0 .2.2.5.6.4A8 8 0 0 0 8 .2Z" />
          </svg>
          <span class="visually-hidden">GitHub</span>
        </a>
      </nav>
    </div>
  </div>
</header>