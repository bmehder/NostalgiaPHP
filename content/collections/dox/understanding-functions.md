---
title: Understanding functions.php in NostalgiaPHP
description: A walkthrough of the helper functions that power routing, rendering, and content loading.
image: static/media/understanding-functions.jpg
date: 2025-09-20
tags: php, simplicity, retro
---

# Understanding `functions.php`

One of the most important files in NostalgiaPHP is **`functions.php`**. It holds the core helper functions that make the whole system work — from rendering templates to parsing Markdown. This post walks through each function and explains what it does.

---

## Path and URL Helpers

### `normalize_path($path)`

Cleans up filesystem paths so they always use forward slashes. This makes things consistent across Windows, macOS, and Linux.

### `config($key, $default)`

Fetches a configuration value from your global settings. Handy for site‑wide options like the site `name` or `base_url`.

### `site($key, $default)`

Shortcut to grab site‑specific config keys (name, description, etc.).

### `path($key, $extra)`

Builds an absolute path inside the project. For example, `path('templates')` points to the templates folder.

### `url($path)`

Generates a site URL. Useful for links inside templates so you don’t hard‑code the domain.

### `request_path()`

Returns the current request path (e.g. `/about`). Used by the router to figure out what content to load.

---

## Content Helpers

### `is_collection($dir)`

Checks if a directory looks like a collection (e.g. `blog/` with multiple Markdown items).

### `read_file($file)`

Reads a file into a string. A thin wrapper, but useful for consistency.

### `parse_front_matter($text)`

Splits the front matter (the `---` metadata block at the top of a Markdown file) from the body content.

### `markdown_to_html($markdown)`

Turns Markdown into HTML. This is what lets you write posts in `.md` files and see them rendered on your site.

### `load_page($slug)`

Loads a page by slug, looking in the `content/pages` directory. Returns both the front matter and the rendered HTML body.

### `sanitize_rel_path($path)`

Ensures a relative path doesn’t contain `../` tricks. Prevents directory traversal vulnerabilities.

### `load_page_path($file)`

Loads a page from a known file path instead of a slug. Similar output to `load_page`.

### `load_collection_item($collection, $slug)`

Loads a single item from a collection (e.g. one blog post).

### `list_collection($collection)`

Lists all items in a collection, sorted and parsed. This powers your collection index pages.

### `excerpt_from_html($html, $length)`

Creates a text excerpt from HTML content, useful for previews on cards or lists.

---

## Templating Helpers

### `nav_link($href, $label, $path)`

Builds a navigation link. Automatically adds an `active` class when the current page matches.

### `is_active($href, $current_path, $prefix)`

Check if a navigation link should be marked "active."

### `active_class($href, $current_path, $prefix, $class)`

Return a CSS class if a link should be marked "active."

### `render($view, $vars)`

Renders a template from the `templates/` folder. Variables like `$title` and `$content` are extracted for use inside the template.

---

## Tags (how NostalgiaPHP reads them)

NostalgiaPHP treats **tags** as plain front-matter. You can write them as a CSV string:

```yaml
---
title: Example
tags: php, simplicity, retro
---
```

Internally, the tag pages scan Markdown files and read front matter via `parse_front_matter()`. They’re tolerant to:
- tags: or tag: (either works)
- casing (Tags, TAG, etc.)
- CSV strings or arrays
- an optional fallback key: `keywords:`

---

## Why keep these in one file?

`functions.php` is small, fast, and easy to read. Every helper is:

* **Global**: Available everywhere in the project.
* **Focused**: Each does one simple job.
* **Composable**: You can combine them to build routes, load content, and render pages.

This file is the glue that holds NostalgiaPHP together — tiny, clear functions instead of a heavy framework.

---

## Next Steps

* Skim through `functions.php` yourself — it’s less than a couple hundred lines.
* Try adding your own helper if you need something site‑wide.
* Use the [Dox collection](/dox) to extend your own project documentation.