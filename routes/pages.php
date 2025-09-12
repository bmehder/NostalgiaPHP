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

$content = $page['html'];

$layout = !empty($meta['layout']) ? $meta['layout'] : 'main';
render($layout, compact('title', 'content', 'path', 'meta'));