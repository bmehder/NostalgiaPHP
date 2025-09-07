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

  <link rel="canonical" href="<?= htmlspecialchars(url($_SERVER['REQUEST_URI'] ?? '/')) ?>">

  <meta property="og:title" content="<?= htmlspecialchars($meta['title'] ?? $title ?? site('name')) ?>">
  <?php if (!empty($meta['description'])): ?>
    <meta property="og:description" content="<?= htmlspecialchars($meta['description']) ?>">
  <?php endif; ?>
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?= htmlspecialchars(url($_SERVER['REQUEST_URI'] ?? '/')) ?>">

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
  <script>
    (function () {
      const btn = document.querySelector('.nav-toggle');
      const nav = document.getElementById('site-nav');
      const inner = document.querySelector('.inner.spread-apart');
      if (!btn || !nav) return;

      btn.addEventListener('click', () => {
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', String(!expanded));
        nav.setAttribute('aria-expanded', String(!expanded));
        inner && inner.classList.toggle('nav-open', !expanded);
      });
    }());
  </script>
</body>

</html>