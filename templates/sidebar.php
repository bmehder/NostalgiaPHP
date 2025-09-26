<?php include path('partials') . '/head.php'; ?>

<body class="layout-sidebar">
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>
      <?= $hero_html ?>
      <main id="main">
      <div class="outer">
        <div class="inner">
          <div class="content-sidebar">
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