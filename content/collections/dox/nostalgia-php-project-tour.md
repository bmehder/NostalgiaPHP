---
title: NostalgiaPHP — Project Tour
description: A tour of the NostalgiaPHP project files.
image: /static/media/home.png
date: 2025-09-02
excerpt: This is a tiny file‑based PHP CMS: Markdown in, HTML out. No database, no framework. Explore the project.
---

# NostalgiaPHP — Project Tour

This is a tiny **file‑based PHP CMS**: Markdown in, HTML out. No database, no framework.

## Top-Level Files
- **index.php** — Router. Reads the URL and decides whether to render a *page*, a *collection list*, or a *collection item*.
- **functions.php** — Helpers: config, paths, URL building, front‑matter parsing, Markdown → HTML, loaders, and render.
- **config.php** — Site settings (name, timezone, base_url) and collection definitions.
- **.htaccess** — Pretty URLs when using Apache in production (everything routes to `index.php`).

## Folders
- **templates/** — Layouts. `main.php` wraps every page with head/foot and includes partials.
- **partials/** — Reusable chunks. `header.php` and `footer.php`.
- **content/** — File‑based content.
  - **pages/** — Static pages (e.g., `about.md`). URL is `/{filename}`.
  - **collections/** — Named groups (e.g., `blog/`). Items are `/{collection}/{slug}`.
- **static/** — CSS, images, etc. Served as static files.

## Routing Flow (index.php)
1. Parse the current request path.
2. If `/` → render `content/pages/index.md`.
3. If `/{collection}` → list items under `content/collections/{collection}`.
4. If `/{collection}/{slug}` → render that item.
5. Else treat as a page: `content/pages/{slug}.md`.

## Template Flow
- Router prepares `$title` and `$content` (HTML).
- `templates/main.php` outputs HTML head, includes `partials/header.php`, dumps `$content`, and includes `partials/footer.php`.

## Content Files
Each `.md` file can start with simple front‑matter followed by Markdown:

```md
---
title: Hello World
date: 2025-09-01
draft: false
---

# Heading

Some **markdown** body text.
```

Supported front‑matter keys are free-form; `date` is auto‑cast to `DateTime` if `YYYY-MM-DD`.

## Collections
Define collections in `config.php`:

```php
'collections' => [
  'blog' => [
    'permalink' => '/blog/{slug}',
    'list_url'  => '/blog',
    'sort'      => ['date','desc'],
  ],
],
```

Create items under `content/collections/blog/*.md`. The filename (without `.md`) is the `slug` unless overridden in front‑matter.

## Helpers Cheat Sheet (functions.php)
- `site($key)` — read site config (e.g., `site('name')`).
- `path($key)` — resolve paths: `pages`, `collections`, `templates`, `partials`.
- `url($path)` — base‑aware URL builder (works in subfolders).
- `request_path()` — current URL path (e.g., `about`, `blog/hello-world`).
- `load_page($slug)` — load `content/pages/{slug}.md`.
- `list_collection($name)` — list items with meta for a collection.
- `load_collection_item($name, $slug)` — load one item.
- `render($view, $vars)` — include template with variables.

## Mental Model for WP Folks
- Think **template hierarchy** without the hierarchy: it’s all handled by `index.php`.
- Think **Loop**, but the “query” is a directory listing for a collection.
- Think **the_content()** being `$content` already HTML‑ified from Markdown.
- Menus are old‑school: edit `partials/header.php` by hand.

## Deploy Notes
- Apache: enable `.htaccess` as included. Nginx: route non‑file requests to `index.php`.
- Set correct `base_url` in `config.php` if serving from a subdirectory.
- Set permissions: directories `755`, files `644`.
