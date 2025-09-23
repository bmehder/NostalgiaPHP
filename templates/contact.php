<?php include path('partials') . '/head.php'; ?>

<body class="contact">
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
      <section>
        <div class="outer">
          <div class="inner">
            <div class="content">
              <h1>Contact Us</h1>
              <p>We’d love to hear from you. Fill out the form below:</p>
              <?php include path('partials') . '/contact-form.php'; ?>
            </div>
          </div>
        </div>
      </section>
    </main>
    <?php include path('partials') . '/footer.php'; ?>
  </div>