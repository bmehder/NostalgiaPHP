<?php include path('partials') . '/head.php'; ?>

<body>
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>
    <?= $hero_html ?>
    <main id="main">
      <?php include path('partials') . '/breadcrumbs.php'; ?>
      <section>
        <div class="outer">
          <?php if ($isItem): ?>
            <div class="inner" style="--inner-padding-block: var(--size-1-5)">
          <?php else: ?>
            <div class="inner">
          <?php endif; ?>
            <div class="content"><?= $content ?></div>
          </div>
        </div>
      </section>
    </main>
    <?php include path('partials') . '/footer.php'; ?>
  </div>
</body>