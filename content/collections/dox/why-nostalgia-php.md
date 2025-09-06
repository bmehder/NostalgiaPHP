---
title: Why NostalgiaPHP?
slug: why-nostalgia-php
date: 2025-09-06
---

# Why NostalgiaPHP?

## The Problem
Most ways to build a website today are overkill:

- **WordPress** → instantly drags in a DB, admin UI, plugins, themes, update cycles, PHP version nags… all for a simple blog.
- **Astro / Next / Eleventy** → modern, fast, static-friendly… but need Node, npm, dependencies, configs, build steps.
- **Jekyll / Hugo** → great static site generators, but you need Ruby or Go, and a build process.

All of them are fantastic at scale. But what if you just want to make a small website — with a few **pages**, some **collections** (like a blog or portfolio), and a couple of **partials** for your header and footer?

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
- **Collections** are just folders of Markdown.
- **Partials** are PHP includes.
- **Templates** are simple PHP files.
- **Assets** are whatever you put in `/assets`.

No database migrations. No npm install. No build pipeline. No JS meta-framework wars.

---

## The Spirit
NostalgiaPHP is the **spiritual heir of early WordPress** — when you could unzip a folder, drop in some files, and publish a blog.  
It’s also the **spiritual cousin of Jekyll and Hugo**, but without requiring a generator or build step.

It’s so minimal you can explain it to a teenager in 5 minutes, but powerful enough to run a small real site.

---

## Who It’s For

- Developers who remember when PHP “just worked.”
- Folks who want to throw up a simple site without databases, migrations, or build tools.
- Designers who want a dead-simple CMS-like flow without logging into an admin dashboard.
- Tinkerers who like Markdown and front matter but don’t want to learn Ruby, Go, or Node.

## Who It’s Not For

- People running a high-traffic news site with dozens of editors.
- Teams that need a WYSIWYG admin panel, workflow, or user accounts.
- Enterprises that expect plugins for everything.
- Anyone allergic to PHP.

---

## Summary
If you just want **simple sites, simply** — NostalgiaPHP is for you.
