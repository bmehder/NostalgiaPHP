---
title: Nosty CLI — Your New Best Friend
description: Scaffold posts and pages with a single command using the Nosty CLI helper.
date: 2025-09-28
image: static/media/nosty-cli.jpg
---

# NostalgiaPHP CLI

A tiny command-line helper for working with NostalgiaPHP sites.

The CLI lives in the project root (`nphp`). Run it with PHP:

```bash
php nphp <command> [args]
```

---

## Commands

### `make:post`

Scaffold a new post in a collection.

```bash
php nphp make:post <collection> <slug> [options]
```

Options:
- `--title="Custom Title"` → Override default title
- `--template=main` → Which front‑matter template (default: `main`)
- `--date=YYYY-MM-DD` → Override date (default: today in site timezone)
- `--draft` → Add `draft: true` to front matter
- `--force` → Overwrite if file already exists

Example:

```bash
php nphp make:post blog hello-world --title="Hello World" --draft
```

---

### `make:page`

Scaffold a new standalone page.

```bash
php nphp make:page <slug> [options]
```

Same options as `make:post`.

Example:

```bash
php nphp make:page about --title="About Us"
```

---

### `backup`

Zip up your entire project into a timestamped archive.

```bash
php nphp backup [--to=/absolute/path]
```

If no `--to` is provided, backups go to `~/nosty_backups/<project>`.

Example:

```bash
php nphp backup --to="$HOME/Dropbox/nosty_backups"
```

---

### Build (SSG)

Pre-render your site into static HTML (Static Site Generation).

This command:
- Copies static/ into the output directory
- Each page is written to disk as {dir}/index.html so that users see clean URLs like /about instead of /about.html.
- Renders all collections (lists + individual items, with pagination)
- Fixes relative asset paths for portability
- Pre-renders dynamic routes like:
  - /tags (list of all tags)
  - /tag/{slug} (all items/pages for a tag)
  - /sitemap.xml (auto-generated sitemap for search engines)
  - /robots.txt (with a link to the sitemap)

>**⚠️ Limitations:** Some pages cannot be safely pre-rendered. For example, the Contact page contains a live form that posts back to the same PHP route. Pre-rendering would freeze it into a static file, breaking submissions. To support forms on static hosting, you’d need to switch to a client-side submission method (JavaScript + API endpoint or a service like Netlify Forms).

<div style="min-height: var(--size)"></div>

```bash
php nphp build [--out=dist] [--clean]
```

Options:
- `--out=dist` → Output directory (default: `dist`)
- `--clean` → Remove existing output directory before building

Examples:

```bash
# Build into /dist
php nphp build

# Clean old build and output to /public
php nphp build --out=public --clean
```

---

## Why It Matters

The CLI helps you:

- Scaffold content quickly (posts, pages)
- Back up your entire project
- Pre-render your site for **static hosting**

With `build`, you can host your NostalgiaPHP site anywhere — Netlify, GitHub Pages, Vercel, S3 — no PHP server required.
