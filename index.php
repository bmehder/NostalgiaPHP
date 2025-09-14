<?php
// index.php — tiny router
require __DIR__ . '/functions.php';
date_default_timezone_set(site('timezone') ?: 'UTC');

// Normalized path & parts
$path = request_path();            // e.g. '/', '/blog', '/blog/hello'
$parts = $path === '/' ? [] : explode('/', ltrim($path, '/'));
$first = $parts[0] ?? '';

// Special files
if ($path === '/robots.txt') {
  require __DIR__ . '/routes/robots.php';
  exit;
}
if ($path === '/sitemap.xml') {
  require __DIR__ . '/routes/sitemap.php';
  exit;
}

// Home
if ($path === '/') {
  require __DIR__ . '/routes/pages.php';
  exit;
}

// Collections: /blog  or  /blog/slug
if (is_collection($first)) {
  require __DIR__ . '/routes/collections.php';
  exit;
}

// Search (server-side)
if ($path === '/search') {
  require __DIR__ . '/routes/search.php';
  exit;
}

// Fallback: nested pages (/about, /guides/install, etc.)
require __DIR__ . '/routes/pages.php';