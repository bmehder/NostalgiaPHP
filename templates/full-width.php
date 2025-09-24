<?php include path('partials') . '/head.php'; ?>

<body class="full-width">
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>
    <?= $hero_html ?>
    <main id="main">
      <div class="content"><?= $content ?></div>
    </main>
    <?php include path('partials') . '/footer.php'; ?>
  </div>