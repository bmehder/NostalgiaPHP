<?php include path('partials') . '/head.php'; ?>

<body class="layout-sidebar">
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>
    <section>
      <?php
        // HERO: render if any hero-related keys exist
        $hasHero = !empty($meta['hero_title']) || !empty($meta['hero']) || !empty($meta['hero_image']);
        if (!empty($meta) && $hasHero) {
          $hero_title = $meta['hero_title'] ?? ($meta['title'] ?? '');
          $hero_subtitle = $meta['hero_subtitle'] ?? ($meta['hero'] ?? '');
          $hero_image = $meta['hero_image'] ?? null;
          include path('partials') . '/hero.php';
        }
        ?>
    </section>
      <main id="main">
      <div class="outer">
        <div class="inner">
          <div class="two-col">
            <section class="content flow">
              <?= $content ?>
            </section>
            <?php include path('partials') . '/sidebar.php'; ?>
          </div>
        </div>
      </div>
    </main>
    <?php include path('partials') . '/footer.php'; ?>
  </div>