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

  <?php
  $canon = $path === '' || $path === '/'
    ? url('/')
    : url('/' . trim($path, '/'));
  ?>
  <link rel="canonical" href="<?= $canon ?>">

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

  <meta name="base-url" content="<?= htmlspecialchars(rtrim(site('base_url'), '/')) ?>">

  <link rel="icon" href="<?= url('/static/favicon.png') ?>" type="image/png">

  <!-- Styles -->
  <link rel="stylesheet" href="<?= url('/static/css/colors.css') ?>">
  <link rel="stylesheet" href="<?= url('/static/css/utilities.css') ?>">
  <link rel="stylesheet" href="<?= url('/static/css/components.css') ?>">
  <link rel="stylesheet" href="<?= url('/static/css/style.css') ?>">

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@600..900&display=swap" rel="stylesheet">

  <!-- Prism -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism.min.css">
  <script src="https://cdn.jsdelivr.net/npm/prismjs/prism.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/prismjs/components/prism-jsx.min.js"></script>

  <!-- Mobile Nav -->
  <script type="module" src="/static/js/nav.js"></script>

  <!-- TODO: hook per-page extra head HTML (inline CSS, fonts, etc.) -->
</head>