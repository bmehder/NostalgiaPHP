<?php
// partials/head.php
// expects: $title (string), $meta (array)

$site_name = site('name') ?? 'Site';
$page_title = trim($title ?? '');
$full_title = $page_title ? ($page_title . ' — ' . $site_name) : $site_name;

$description = $meta['description'] ?? '';

// Open Graph fallbacks
$og_title = $meta['og_title'] ?? ($page_title ?: $site_name);
$og_desc = $meta['og_description'] ?? $description;
$og_image = $meta['og_image'] ?? null; // optional: set in front matter when available

// ----- Build absolute base (proxy-safe) -----
$cfgBase = trim((string) (config()['site']['base_url'] ?? ''), '/');

$scheme =
  (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO']
    : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http'));

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

if (preg_match('~^https?://~i', (string) (config()['site']['base_url'] ?? ''))) {
  $absBase = rtrim((string) config()['site']['base_url'], '/');
} else {
  $absBase = rtrim($scheme . '://' . $host . ($cfgBase ? '/' . $cfgBase : ''), '/');
}

// ----- Canonical URL (strip ?page=1 anywhere in query) -----
$reqUri = $_SERVER['REQUEST_URI'] ?? '/';
$canonPath = preg_replace('~([?&])page=1(&|$)~', '$1', $reqUri);
$canonPath = preg_replace('~[?&]$~', '', $canonPath);
$canonical = $absBase . $canonPath;

// Derive current path (no domain) for OG:url too
$pathOnly = parse_url($reqUri, PHP_URL_PATH) ?? '/';
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= htmlspecialchars($full_title, ENT_QUOTES, 'UTF-8') ?></title>

  <?php if ($description): ?>
    <meta name="description" content="<?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?>">
  <?php endif; ?>

  <link rel="canonical" href="<?= htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') ?>">

  <!-- Favicon core -->
  <link rel="apple-touch-icon" sizes="180x180" href="/static/favicons/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="/static/favicons/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/static/favicons/favicon-16x16.png">
  <link rel="manifest" href="/static/favicons/site.webmanifest">
  <link rel="mask-icon" href="/static/favicons/safari-pinned-tab.svg" color="#000000">
  <link rel="shortcut icon" href="/static/favicons/favicon.ico">
  <meta name="msapplication-TileColor" content="#000000">
  <meta name="msapplication-config" content="/static/favicons/browserconfig.xml">
  <meta name="theme-color" content="#000000">

  <?php
  // ----- rel=prev/next for paginated collection lists -----
  if (!empty($meta['pagination'])):
    $p = max(1, (int) ($meta['pagination']['page'] ?? 1));
    $pages = max(1, (int) ($meta['pagination']['pages'] ?? 1));

    // Derive first segment to detect if we’re on a collection list page
    $seg0 = explode('/', trim($pathOnly, '/'))[0] ?? '';
    if ($seg0 && is_collection($seg0)):
      $pageHref = function (int $n) use ($seg0) {
        return url('/' . $seg0 . ($n > 1 ? ('?page=' . $n) : ''));
      };
      if ($p > 1): ?>
        <link rel="prev" href="<?= htmlspecialchars($pageHref($p - 1), ENT_QUOTES, 'UTF-8') ?>">
      <?php endif; ?>
      <?php if ($p < $pages): ?>
        <link rel="next" href="<?= htmlspecialchars($pageHref($p + 1), ENT_QUOTES, 'UTF-8') ?>">
      <?php endif; ?>
    <?php endif; ?>
  <?php endif; ?>

  <!-- Open Graph (basic) -->
  <meta property="og:type" content="website">
  <meta property="og:title" content="<?= htmlspecialchars($og_title, ENT_QUOTES, 'UTF-8') ?>">
  <?php if ($og_desc): ?>
    <meta property="og:description" content="<?= htmlspecialchars($og_desc, ENT_QUOTES, 'UTF-8') ?>">
  <?php endif; ?>
  <meta property="og:url" content="<?= htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') ?>">
  <?php if ($og_image): ?>
    <meta property="og:image" content="<?= htmlspecialchars($og_image, ENT_QUOTES, 'UTF-8') ?>">
  <?php endif; ?>

  <!-- Useful for client scripts that need the base URL -->
  <meta name="base-url" content="<?= htmlspecialchars(rtrim(site('base_url'), '/'), ENT_QUOTES, 'UTF-8') ?>">

  <!-- Apply saved theme class before CSS loads to prevent flash of default skin -->
  <script>
    (function () {
      try {
        const theme = localStorage.getItem('nostalgia:theme');
        if (theme) document.documentElement.classList.add(theme);
      } catch (_) { }
    })();
  </script>

  <!-- Styles -->
  <link rel="stylesheet" href="<?= url('/static/css/colors.css') ?>">
  <link rel="stylesheet" href="<?= url('/static/css/reboot.css') ?>">
  <link rel="stylesheet" href="<?= url('/static/css/skins.css') ?>">
  <link rel="stylesheet" href="<?= url('/static/css/utilities.css') ?>">
  <link rel="stylesheet" href="<?= url('/static/css/components.css') ?>">

  <!-- Google Font (consider self-hosting for perf/privacy) -->
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter+Tight:wght@600..900&display=swap" rel="stylesheet">

  <!-- Prism (non-blocking) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism-okaidia.min.css" media="print"
    onload="this.media='all'">
  <noscript>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs/themes/prism-okaidia.min.css">
  </noscript>

  <script>
    // Configure autoloader path before plugin loads
    window.Prism = window.Prism || {};
    Prism.plugins = Prism.plugins || {};
    Prism.plugins.autoloader = { languages_path: "https://cdn.jsdelivr.net/npm/prismjs/components/" };
  </script>
  <script defer src="https://cdn.jsdelivr.net/npm/prismjs/prism.min.js"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/prismjs/plugins/autoloader/prism-autoloader.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/prismjs/plugins/toolbar/prism-toolbar.css" media="print"
    onload="this.media='all'">

  <!-- Theme Switcher -->
  <script type="module" src="/static/js/apps/theme-switcher.js"></script>
</head>