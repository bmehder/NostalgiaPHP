<?php
// index.php — tiny router
require __DIR__ . '/functions.php';
date_default_timezone_set(site('timezone') ?: 'UTC');

/**
 * Figure out what part of the site is being requested.
 *
 * $path   → the full request path (no domain, no query string),
 *           e.g. '/', '/blog', '/blog/hello'.
 *
 * $parts  → the path split into segments. The home page '/' is treated
 *           as an empty array []. For '/blog/hello' you get ['blog','hello'].
 *
 * $first  → the first segment of the path, or '' if none.
 *           Used to decide whether this is a collection ('blog'),
 *           a page ('about'), or the site root ('').
 */
$path = request_path();            // e.g. '/', '/blog', '/blog/hello'
$parts = $path === '/' ? [] : explode('/', ltrim($path, '/'));
$first = $parts[0] ?? '';

if ($path === '/robots.txt') {
  require __DIR__ . '/routes/robots.php';
  exit;
}

if ($path === '/sitemap.xml') {
  require __DIR__ . '/routes/sitemap.php';
  exit;
}

if ($path === '/admin') {
  require __DIR__ . '/routes/admin.php';
  exit;
}

if ($path === '/search') {
  require __DIR__ . '/routes/search.php';
  exit;
}

// Tags
if ($path === '/tags') {
  require __DIR__ . '/routes/tags.php';
  exit;
}

if ($first === 'tag' && isset($parts[1])) {
  require __DIR__ . '/routes/tag.php';
  exit;
}

// Collections: /blog  or  /blog/slug
if (is_collection($first)) {
  require __DIR__ . '/routes/collections.php';
  exit;
}

require __DIR__ . '/routes/pages.php';