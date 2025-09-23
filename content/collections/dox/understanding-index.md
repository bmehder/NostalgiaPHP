---
title: Understanding index.php in NostalgiaPHP
description: A guided tour of the tiny front controller that routes requests, loads content, and renders templates.
image: static/media/1.jpg
date: 2025-09-20
template: main
tags: php, simplicity, retro
---
# Understanding index.php

In NostalgiaPHP, index.php is the front controller—the single entry point that receives every request, figures out what the user wants, loads content, and renders the right template. This post walks through the core flow so you can extend it with confidence.

---

## 1. Normalize the request path

```php
$path  = request_path();  // '/', '/about', '/blog/hello-world'
$parts = $path === '/' ? [] : explode('/', ltrim($path, '/'));
$first = $parts[0] ?? '';
```
- `request_path()` strips the domain and query string.
- Home becomes [] for simpler branching.
- `$first` is the key decision point (e.g., blog, about).

*Tip: Keep this logic tiny and readable—most routing bugs start here.*

---

## 2. Handle special routes (system endpoints)

Examples: `robots.txt`, `sitemap.xml`, `admin`.

```php
if ($path === '/robots.txt') {
  require __DIR__ . '/routes/robots.php';
  exit;
}

if ($path === '/sitemap.xml') {
  require __DIR__.'/sitemap.php';
  exit;
}

if ($path === '/admin') {
  // require __DIR__.'/auth/guard.php'; require_login();
  require __DIR__.'/admin.php';
  exit;
}
```

These bypass the normal content lookup entirely and loads the `admin.php` route.

---

## 3. Tag routes (cross-collection)

Two tiny routes enable a global tag system:

a. `/tags` — list all tags with counts

```php
if ($path === '/tags') {
  require __DIR__ . '/routes/tags.php';
  exit;
}
```
b. `/tag/{slug}` — list items with a given tag

```php
if ($first === 'tag' && isset($parts[1])) {
  require __DIR__ . '/routes/tag.php';
  exit;
}
```
These run before the collection/page branches.

They scan `content/collections/*.md` and `content/pages/*.md`, parse front matter, and match tags (or tag/keywords) case-insensitively.

Rendering is intentionally minimal (a simple list), so you can swap it for cards later without touching the router.

The `tags.php` or `tag.php` route is loaded.

---

## 4. Collection routes (/collection/slug)

```php
if (is_collection($first)) {
  require __DIR__ . '/routes/collections.php';
  exit;
}
```
If `$first` is a collection in the `config.php` file → load the `collections.php` route.

---

## 5. Page routes (/, /about, /about/blink)

```php
require __DIR__ . '/routes/pages.php';
```

If no other routes match, assume it is a page and load the `pages.php` route.

---

## 6. 404 fallback

```php
http_response_code(404);
render('main', [
  'title'   => 'Not Found',
  'content' => '<h1>404</h1><p>Page not found.</p>',
  'path'    => $path,
]);
```
Each route will return a 404 page if the content is not fount.

---

## Design notes
- Small surface area: The router is just branching logic + a few helpers. No classes, no framework.
- Predictable structure: Pages live in `content/pages`, collections in `content/collections/{name}`.
- Pluggable views: Index pages use partials (`cards-grid.php`, `items-list.php`) so theme changes don’t touch routing.
- Security: Always run slugs through `sanitize_rel_path()` before hitting the filesystem.

---

## TL;DR

`index.php` is intentionally tiny. It:
1. Parses the path
2. Short-circuits special routes
3. Then tries collection
4. Then tries home
5. Falls back to a 404 page

This clarity is the whole point of NostalgiaPHP: a site is just folders and files wired together with a few small functions.