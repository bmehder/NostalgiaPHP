---
title: Understanding index.php in NostalgiaPHP
description: A guided tour of the tiny front controller that routes requests, loads content, and renders templates.
date: 2025-09-20
template: main
tags: php, simplicity, retro
---
# Understanding index.php

In NostalgiaPHP, index.php is the front controller—the single entry point that receives every request, figures out what the user wants, loads content, and renders the right template. This post walks through the core flow so you can extend it with confidence.

---

1. Normalize the request path

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

2. Handle special routes (system endpoints)

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

These bypass the normal content lookup entirely.

---

3. Tag routes (cross-collection)

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
- These run before the collection/page branches.
- They scan `content/collections/*.md` and `content/pages/*.md`, parse front matter, and match tags (or tag/keywords) case-insensitively.
- Rendering is intentionally minimal (a simple list), so you can swap it for cards later without touching the router.

---

4. Collection routes (`/collection/slug`)

```php
if ($first && is_dir(path('content')."/collections/$first")) {
  // Single item? e.g. /blog/hello-world
  if (isset($parts[1]) && $parts[1] !== '') {
    $slug = sanitize_rel_path($parts[1]);
    $item = load_collection_item($first, $slug);

    if ($item) {
      render('main', [
        'title'     => $item['frontmatter']['title'] ?? slug_to_title($slug),
        'content'   => $item['content'],
        'path'      => $path,
        'meta'      => $item['frontmatter'] ?? [],
        'hero_html' => ''
      ]);
      exit;
    }
  }

  // Otherwise, render the collection index (cards or list)
  $items = list_collection($first);
  $html  = (function() use ($items) {
    ob_start();
    include path('partials').'/cards-grid.php'; // or items-list.php
    return ob_get_clean();
  })();

  render('main', [
    'title'   => slug_to_title($first),
    'content' => $html,
    'path'    => $path,
  ]);
  exit;
}
```
- If `/blog/:slug` exists → render the item page.
- Else `/blog` → render the collection index using a partial.

---

5. Page routes (`/about`)

```php
if ($first) {
  $page = load_page($first);
  if ($page) {
    render('main', [
      'title'   => $page['frontmatter']['title'] ?? slug_to_title($first),
      'content' => $page['content'],
      'path'    => $path,
      'meta'    => $page['frontmatter'] ?? [],
    ]);
    exit;
  }
}
```

This looks for `content/pages/{slug}.md` and renders it if found.

---

6. Home page (`/`)

```php
if ($path === '/') {
  $home = load_page('index');
  if ($home) {
    render('main', [
      'title'   => $home['frontmatter']['title'] ?? site('name'),
      'content' => $home['content'],
      'path'    => $path,
    ]);
    exit;
  }
}
```
Treat `content/pages/index.md` as the root content.

---

7. 404 fallback

```php
http_response_code(404);
render('main', [
  'title'   => 'Not Found',
  'content' => '<h1>404</h1><p>Page not found.</p>',
  'path'    => $path,
]);
```
A clear, final fallback keeps the router predictable.

---

## Design notes
- Small surface area: The router is just branching logic + a few helpers. No classes, no framework.
- Predictable structure: Pages live in `content/pages`, collections in `content/collections/{name}`.
- Pluggable views: Index pages use partials (`cards-grid.php`, `items-list.php`) so theme changes don’t touch routing.
- Security: Always run slugs through `sanitize_rel_path()` before hitting the filesystem.

---

## Extending the router
- Add more static routes by mapping `$path` to scripts, just like `sitemap.php`.
- Support alternate views via query string or front matter:

```php
$view = ($_GET['view'] ?? 'cards') === 'list' ? 'items-list.php' : 'cards-grid.php';
```

- Inject hero sections by setting `hero_html` in `render()` vars.

---

## TL;DR

`index.php` is intentionally tiny. It:
1. Parses the path
2.	Short-circuits special routes
3.	Tries collection item → collection index → page → home
4.	Falls back to 404

This clarity is the whole point of NostalgiaPHP: a site is just folders and files wired together with a few small functions.