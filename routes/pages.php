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

$template = !empty($meta['template']) ? $meta['template'] : 'main';

// routes/pages.php — after you’ve loaded the page/front matter and chosen $tpl
$vars = [
  'title' => $title,
  'content' => $content,
  'meta' => $meta,
  'path' => $path,
];

// If this page uses the home template, attach latest blog items
if (($meta['template'] ?? '') === 'home' && function_exists('list_collection')) {
  $vars['blog_items'] = array_slice(list_collection('blog') ?? [], 0, 3);
}

// render('home', $vars);
render($template, $vars);