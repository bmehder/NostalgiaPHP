<!DOCTYPE html>
<html lang="en">
<?php include path('partials') . '/head.php'; ?>

<body class="landing">
  <main id="main" tabindex="-1">
    <?php if (!empty($hero_html))
      echo $hero_html; ?>
    <section class="page-content">
      <?= $content ?>
    </section>
  </main>
</body>

</html>