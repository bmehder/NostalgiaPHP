---
title: Why NostalgiaPHP?
description: Why was NostalgiaPHP created?
date: 2025-09-08
image: /static/media/why-nostalgia-php.jpg
tags: php, simplicity, retro
---

# Why NostalgiaPHP?

## The Problem
Building a simple website today often comes with a lot of extra baggage:

- **WordPress** → instantly drags in a DB, admin UI, plugins, themes, update cycles, PHP version nags, etc.
- **Astro / Next / Eleventy** → modern, fast, static-friendly… but need Node, npm, dependencies, configs, build steps.
- **Jekyll / Hugo** → great static site generators, but you need Ruby or Go, and a build process. Server side rendering has many more advantages.

They’re all fantastic at scale. But what if you just need a small site — a few pages, a blog or portfolio, and a header and footer?

---

## The Solution
**NostalgiaPHP**:

- Built in plain PHP.
- Zero database.
- Zero build step.
- Zero frameworks.

Just **Markdown files** with front matter → served as pages and collections.  
Drop them in `/content/pages` or `/content/collections/{name}/` and you’re live.

Run one command:

```bash
php -S localhost:8000
```

…and you’re looking at your site.

---

## The Philosophy

- **Pages** are Markdown files.
- **Collections** are just folders of Markdown files.
- **Partials** are PHP includes.
- **Templates** are simple PHP files.
- **Assets** are whatever you put in `/static`.

No database migrations. No npm install. No build pipeline. No JS metaframeworks.
