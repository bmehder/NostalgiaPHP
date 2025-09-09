<?php
// partials/head.php
// expects: $title (string), $meta (array)

$site_name = site('name') ?? 'Site';
$page_title = trim($title ?? '');
$full_title = $page_title ? ($page_title . ' — ' . $site_name) : $site_name;

$description = $meta['description'] ?? '';
$canonical = rtrim(site('base_url') ?: '/', '/') . ($_SERVER['REQUEST_URI'] ?? '/');

// Optional Open Graph fallbacks
$og_title = $meta['og_title'] ?? $page_title ?: $site_name;
$og_desc = $meta['og_description'] ?? $description;
$og_image = $meta['og_image'] ?? null; // set in front matter if you have one
?>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($full_title) ?></title>
  <?php if ($description): ?>
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
  <?php endif; ?>

  <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">

  <!-- Open Graph (basic) -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars($og_title) ?>">
  <?php if ($og_desc): ?>
    <meta property="og:description" content="<?= htmlspecialchars($og_desc) ?>">
  <?php endif; ?>
  <meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
  <?php if ($og_image): ?>
    <meta property="og:image" content="<?= htmlspecialchars($og_image) ?>">
  <?php endif; ?>

  <!-- Styles -->
  <link rel="stylesheet" href="<?= url('/static/css/style.css') ?>">

  <script type="module">
    const btn   = document.querySelector('[data-nav-toggle]');
    const nav   = document.querySelector('[data-site-nav]');
    const inner = document.querySelector('[data-inner-header]');

    if (btn && nav) {
      btn.addEventListener('click', () => {
        const expanded = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', String(!expanded));
        nav.setAttribute('aria-expanded', String(!expanded));
        if (inner) inner.classList.toggle('nav-open', !expanded);
      });
    }
  </script>

  <?php
  // Optional hook: per-page extra head HTML (inline CSS, fonts, etc.)
  if (!empty($meta['head_html']))
    echo $meta['head_html'];
  ?>
</head>