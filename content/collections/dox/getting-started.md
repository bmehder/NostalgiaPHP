---
title: Getting Started
description: The guide to getting started with NostalgiaPHP.
date: 2025-09-07
---

# Getting Started with NostalgiaPHP

NostalgiaPHP is a tiny, file-driven CMS.  
No database. No build step. No CLI wizard. Just unzip, drop in some Markdown files, and you’re publishing.

---

## Requirements

- PHP **7.4+** (works great on PHP 8.x)
- A web server with rewrites enabled (Apache with `.htaccess` or Nginx with `try_files`)
- That’s it. No Node, no npm, no Composer, no database.

---

## Quickstart (local dev)

1. Clone or unzip the project:

   ```bash
   git clone https://github.com/bmehder/NostalgiaPHP.git
   cd NostalgiaPHP-main
   ```

2. Start PHP’s built-in server:

   ```bash
   php -S localhost:8000
   ```

3. Visit [http://localhost:8000](http://localhost:8000) in your browser.

---

## Project Structure

```
nostalgia-php/
├── static/           # images, css, js
├── content/
│   ├── pages/        # static pages (Markdown)
│   └── collections/  # e.g. blog, docs, portfolio
├── partials/         # header, footer, hero, card, gallery, etc
├── templates/        # main layout(s)
├── config.php        # site settings
├── functions.php     # helpers
├── index.php         # router
└── sitemap.php       # sitemap generator
```

- **Pages** live in `content/pages/` as `.md` files.  
  Example: `about.md` → `/about/`.
- **Collections** are folders inside `content/collections/`.  
  Example: `content/collections/blog/hello-world.md` → `/blog/hello-world/`.
- **Partials** (`partials/`) are reusable chunks (like header/footer/cards).  
- **Templates** (`templates/`) define overall layout.

---

## First Edits

1. Change the site name in `config.php`:

   ```php
   'site' => [
     'name' => 'My First NostalgiaPHP Site',
     'base_url' => '/',
     'timezone' => 'Europe/London',
   ],
   ```

2. Edit `content/pages/index.md` to change the homepage text.

3. Add a new page:

   - Create `content/pages/about.md`:

     ```md
     ---
     title: About Us
     description: Learn more about our team.
     ---
     # About Us

     We’re building simple sites with simple tools.
     ```

   - Visit <http://localhost:8000/about/>.

---

## Deployment

- **Apache**: use the included `.htaccess` for pretty URLs.
- **Nginx**: add `try_files $uri $uri/ /index.php?$query_string;`.

---

✅ That’s it — you’re live.  
