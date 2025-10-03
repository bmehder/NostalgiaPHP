<a class="visually-hidden" id="top"></a>

<header>
  <a href="#main" class="skip-link">Skip to main content</a>
  <div class="outer">
    <div class="inner spread-apart" data-inner-header>
      <!-- <a class="brand" href="<?= url('/') ?>"><?= htmlspecialchars(site('name')) ?></a> -->

      <a class="brand flex align-items-center gap-0-5" href="<?= url('/') ?>">
        <!-- NostalgiaPHP mark -->
        <svg aria-label="NostalgiaPHP" width="28" height="28" viewBox="0 0 24 24" fill="var(--accent-strong)">
          <rect x="2.25" y="2.25" width="19.5" height="19.5" rx="6" stroke="currentColor" stroke-width="1.5" />
          <!-- Single-stroke 'N' -->
          <path d="M7 16V8L17 16V8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
            stroke-linejoin="round" />
        </svg>
        <?= htmlspecialchars(site('name')) ?>
      </a>

      <!-- ✅ hidden control for JS-less toggle -->
      <input type="checkbox" id="nav-check" class="nav-check" aria-label="Toggle menu" tabindex="-1">

      <!-- ✅ label becomes the hamburger button -->
      <label for="nav-check" class="nav-toggle" aria-controls="site-nav">
        <span class="visually-hidden">Menu</span>
        <svg viewBox="0 0 24 24" width="40" height="40" fill="var(--stone-100)" aria-hidden="true">
          <rect x="3" y="6" width="18" height="2" rx="1"></rect>
          <rect x="3" y="11" width="18" height="2" rx="1"></rect>
          <rect x="3" y="16" width="18" height="2" rx="1"></rect>
        </svg>
      </label>

      <?php
      $menuFile = __DIR__ . '/../../static/data/menu.json';
      $menu = json_decode(file_get_contents($menuFile), true);

      echo '<nav id="site-nav" data-site-nav class="nav spread-apart">';
      // Load menu.json safely
      $menu = [];
      $menuFile = path('data') . '/menu.json';
      if (is_file($menuFile)) {
        $raw = file_get_contents($menuFile);
        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
          $menu = $decoded;
        }
      }

      // Small esc helper
      $h = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');

      // Render menu items
      foreach ($menu as $item) {
        $href = $item['href'] ?? '#';
        $label = $item['label'] ?? '';
        $submenu = $item['submenu'] ?? [];
        $external = !empty($item['external']);
        $target = $external ? ' target="_blank" rel="noopener"' : '';
        $isActive = active_class($href, $path, true); // your existing helper
      
        if ($submenu) {
          echo '<div class="has-submenu">';
          echo '<a href="' . $h($href) . '" class="top-link ' . $h($isActive) . '" anchor-name="--parent-nav-item">' . $h($label) . '</a>';
          echo '<ul class="submenu list-style-none flow-0-5">';
          foreach ($submenu as $sub) {
            $shref = $sub['href'] ?? '#';
            $slabel = $sub['label'] ?? '';
            echo '<li>' . nav_link($shref, $slabel, $path) . '</li>';
          }
          echo '</ul>';
          echo '</div>';
        } else {
          // If you want a special GitHub icon for the GitHub item, detect by label or add "icon": "github" in JSON
          if ($external && stripos($label, 'github') !== false) {
            echo '<a href="' . $h($href) . '" class="github-link no-border" style="line-height:0"' . $target . '>';
            echo '  <svg viewBox="0 0 16 16" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M8 .2a8 8 0 0 0-2.5 15.6c.4.1.6-.2.6-.4v-1.4c-2.5.5-3-1.2-3-1.2-.3-.9-.8-1.2-.8-1.2-.7-.5.1-.5.1-.5.8.1 1.2.8 1.2.8.7 1.2 1.9.9 2.4.7.1-.5.3-.9.5-1.1-2-.2-4.1-1-4.1-4.3 0-1 .4-1.9 1-2.6-.1-.2-.4-1.1.1-2.3 0 0 .8-.2 2.6 1a9 9 0 0 1 4.7 0c1.8-1.2 2.6-1 2.6-1 .5 1.2.2 2.1.1 2.3.7.7 1 1.6 1 2.6 0 3.3-2.1 4-4.1 4.3.3.2.5.6.5 1.3v1.9c0 .2.2.5.6.4A8 8 0 0 0 8 .2Z"/></svg>';
            echo '  <span class="visually-hidden">' . $h($label) . '</span>';
            echo '</a>';
          } else {
            echo nav_link($href, $label, $path);
          }
        }
      }
      echo '</nav>';
      ?>
    </div>
  </div>
</header>