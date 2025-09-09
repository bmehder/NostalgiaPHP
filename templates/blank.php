<!DOCTYPE html>
<html lang="en">
<?php include path('partials') . '/head.php'; ?>

<body class="blank">
  <main>
    <?php if (!empty($hero_html))
      echo $hero_html; ?>
    <section class="page-content">
      <div class="outer">
        <div class="inner">
          <div class="content"><?= $content ?></div>
        </div>
      </div>
    </section>
  </main>
</body>

</html>