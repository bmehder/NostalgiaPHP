<!DOCTYPE html>
<html lang="en">
<?php include path('partials') . '/head.php'; ?>

<body class="layout-sidebar">
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>
    <main id="main" tabindex="-1">
      <?php if (!empty($hero_html))
        echo $hero_html; ?>
      <div class="outer">
        <div class="inner">
          <div class="two-col">
            <section class="page-content flow">
              <?= $content ?>
            </section>
            <?php include path('partials') . '/sidebar.php'; ?>
          </div>
        </div>
      </div>
    </main>
    <?php include path('partials') . '/footer.php'; ?>
  </div>
</body>

</html>