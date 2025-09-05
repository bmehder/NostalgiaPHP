<?php $siteName = htmlspecialchars(site('name')); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars(($title ?? 'Untitled') . ' — ' . $siteName) ?></title>
  <link rel="stylesheet" href="<?= url('assets/css/site.css') ?>">
</head>
<body>
  <?php include path('partials') . '/header.php'; ?>
  <main class="container">
    <?= $content ?>
  </main>
  <?php include path('partials') . '/footer.php'; ?>
</body>
</html>
