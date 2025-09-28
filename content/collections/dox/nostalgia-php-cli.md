---
title: Nosty CLI — Your New Best Friend
description: Scaffold posts and pages with a single command using the Nosty CLI helper.
date: 2025-09-28
image: static/media/nosty-cli.jpg
template: main
---

# Nosty CLI — Your New Best Friend

Meet **Nosty CLI**, a tiny helper script included with NostalgiaPHP.

With it, you can scaffold new blog posts or pages right from the command line — no more copy‑pasting front‑matter by hand.

## Usage

The CLI lives in the project root. Run it like so:

```bash
php nphp make:post blog hello-world
php nphp make:page about/contact
```

- `make:post` creates `content/collections/{collection}/{slug}.md`
- `make:page` creates `content/pages/{slug}/index.md` (foldered pages for easy assets).

Both commands accept the same options:

- `--title="Custom Title"` (defaults to a titleized slug)
- `--template=main` (choose your template)
- `--date=YYYY-MM-DD` (defaults to today in your site timezone)
- `--draft` (adds `draft: true`)
- `--force` (overwrite if the file exists)

## Examples

```bash
# Blog post
php nphp make:post blog hello-world --title="Hello, World" --template=main --draft

# Docs page (folder + index.md)
php nphp make:page getting-started --title="Getting Started" --template=main
```

That yields front-matter like:

```yaml
---
title: Hello, World
description:
date: 2025-09-28
template: main
draft: true
---
```

## Why It Matters

Consistency, speed, and fewer typos.  

Let Nosty handle the boilerplate, and you can focus on writing.

