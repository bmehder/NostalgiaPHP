<?php $siteName = htmlspecialchars(site('name')); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>
    <?= htmlspecialchars(($meta['title'] ?? $title ?? site('name')) . ' · ' . site('name')) ?>
  </title>
  
  <?php if (!empty($meta['description'])): ?>
    <meta name="description" content="<?= htmlspecialchars($meta['description']) ?>">
  <?php endif; ?>
  <link rel="stylesheet" href="<?= url('assets/css/site.css') ?>">
</head>
<body>
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>
    <main class="flow">
      <div class="outer">
        <div class="inner"><?= $content ?></div>
      </div>
    </main>
    <?php include path('partials') . '/footer.php'; ?>
  </div>
</body>
</html>
