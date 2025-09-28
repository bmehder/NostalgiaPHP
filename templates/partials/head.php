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

<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($full_title) ?></title>
  <?php if ($description): ?>
    <meta name="description" content="<?= htmlspecialchars($description) ?>">
  <?php endif; ?>

  <?php
  $cfgBase = trim((string) (config()['site']['base_url'] ?? ''), '/');

  // Detect scheme (proxy-safe)
  $scheme =
    (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO']
      : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http'));

  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

  if (preg_match('~^https?://~i', (string) (config()['site']['base_url'] ?? ''))) {
    $absBase = rtrim((string) config()['site']['base_url'], '/');
  } else {
    $absBase = rtrim($scheme . '://' . $host . ($cfgBase ? '/' . $cfgBase : ''), '/');
  }

  // Current path
  $path = request_path();
  ?>
  <link rel="canonical" href="<?= $absBase . $path ?>">

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

  <!-- Apply saved theme class before CSS loads to prevent flash of default skin -->
  <script>
    (function () {
      try {
        const theme = localStorage.getItem('nostalgia:theme')
        theme && document.documentElement.classList.add(theme)
      } catch (error) {
        console.error(error)
      }
    })();
  </script>

  <!-- Styles -->
  <link rel="stylesheet" href="<?= url('/static/css/colors.css') ?>">
  <link rel="stylesheet" href="<?= url('/static/css/reboot.css') ?>">
  <link rel="stylesheet" href="<?= url('/static/css/skins.css') ?>">
  <link rel="stylesheet" href="<?= url('/static/css/utilities.css') ?>">
  <link rel="stylesheet" href="<?= url('/static/css/components.css') ?>">

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@600..900&display=swap" rel="stylesheet">

  <!-- Prism (non-blocking) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism-okaidia.min.css" media="print"
    onload="this.media='all'">
  <noscript>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism-okaidia.min.css">
  </noscript>

  <script>
    // configure autoloader path before the plugin loads
    window.Prism = window.Prism || {};
    Prism.plugins = Prism.plugins || {};
    Prism.plugins.autoloader = { languages_path: "https://cdn.jsdelivr.net/npm/prismjs/components/" };
  </script>
  <script defer src="https://cdn.jsdelivr.net/npm/prismjs/prism.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/prismjs/plugins/autoloader/prism-autoloader.min.js"></script>

  <!-- Theme Switcher -->
  <script type="module" src="/static/js/apps/theme-switcher.js"></script>
</head>