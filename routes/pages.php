<?php
// routes/pages.php
// Uses: $path, $parts  (provided by index.php)

if ($path === '/') {
  // Home page -> content/pages/index.md
  $page = load_page('index');
} else {
  // Nested pages: /guides/install -> content/pages/guides/install(.md|/index.md)
  $rel = implode('/', $parts);
  $rel = trim($rel, '/');
  $page = load_page_path($rel);
}

if (!$page) {
  require __DIR__ . '/404.php';
  exit;
}

$meta = $page['meta'] ?? [];
$title = $meta['title'] ?? ucfirst(basename($path));

// Optional hero via front matter
// $hero_html = '';
// if (!empty($meta['hero_title']) || !empty($meta['hero'])) {
//   ob_start();
//   $hero_title = $meta['hero_title'] ?? ($meta['title'] ?? '');
//   $hero_subtitle = $meta['hero_subtitle'] ?? ($meta['hero'] ?? '');
//   $hero_image = $meta['hero_image'] ?? null;
//   include path('partials') . '/hero.php';
//   $hero_html = ob_get_clean();
// }

$content = $page['html'];

$layout = !empty($meta['layout']) ? $meta['layout'] : 'main';
render($layout, compact('title', 'content', 'path', 'meta'));