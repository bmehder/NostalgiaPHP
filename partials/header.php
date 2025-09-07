<header class="site-header">
  <div class="outer">
    <div class="inner spread-apart">
      <a class="brand" href="<?= url('/') ?>"><?= htmlspecialchars(site('name')) ?></a>

      <!-- add this button -->
      <button class="nav-toggle" aria-expanded="false" aria-controls="site-nav">
        <span class="visually-hidden">Menu</span> ☰
      </button>

      <nav id="site-nav" class="nav spread-apart">
        <?= nav_link('/', 'Home', $path) ?>
        <?= nav_link('/about', 'About', $path) ?>
        <?= nav_link('/blog', 'Blog', $path) ?>
        <?= nav_link('/tags', 'Tags', $path) ?>
        <?= nav_link('/dox', 'Dox', $path) ?>
        <?= nav_link('/contact', 'Contact', $path) ?>
        <a href="https://github.com/bmehder/NostalgiaPHP" class="github-link" target="_blank" rel="noopener">
          <!-- … your SVG … -->
          <span class="visually-hidden">GitHub</span>
        </a>
      </nav>
    </div>
  </div>
</header>