<!doctype html>
<html lang="en">

<?php include path('partials') . '/head.php'; ?>

<body class="blog">
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>
    <main>
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
      <section id="page-content">
        <div class="outer" style="background-color: var(--gray-100);">
          <div class="inner" style="--inner-padding-block: var(--size-3);">
            <div class="content"><?= $content ?></div>
          </div>
        </div>
      </section>
    </main>
    <?php include path('partials') . '/footer.php'; ?>
  </div>
</body>

</html>