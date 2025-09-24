<?php include path('partials') . '/head.php'; ?>

<body class="collection" style="background-color: var(--stone-100)">
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>
    <?= $hero_html ?>
    <main id="main">
      <section>
        <div class="outer">
          <div class="inner">
            <div class="content"><?= $content ?></div>
          </div>
        </div>
      </section>
    </main>
    <?php include path('partials') . '/footer.php'; ?>
  </div>