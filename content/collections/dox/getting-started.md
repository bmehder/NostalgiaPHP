---
title: Getting Started
description: The updated guide to getting started with NostalgiaPHP.
date: 2025-09-12
---

# Getting Started with NostalgiaPHP

NostalgiaPHP is a **caveman-simple, file-based CMS**.  
No database. No build step. No framework. Just drop in some Markdown files, and you’re publishing.

---

## Requirements

- PHP **8.0+** (works on modern PHP versions)
- A web server with rewrites enabled (Apache `.htaccess` or Nginx `try_files`)
- That’s it. No Node, no npm, no Composer, no database.

---

## Quickstart (local dev)

1. Clone or download the project:

   ```bash
   git clone https://github.com/bmehder/NostalgiaPHP.git
   cd NostalgiaPHP
   ```

2. Start PHP’s built-in server:

   ```bash
   php -S localhost:8000
   ```

3. Open [http://localhost:8000](http://localhost:8000) in your browser.

---

## Project Structure

```
nostalgia-php/

│── routes/           # routing logic (pages, collections, admin, etc.)
├── content/
│   ├── pages/        # static pages (Markdown)
│   └── collections/  # groups like blog, docs
├── partials/         # header, footer, hero, card, etc.
├── templates/        # layouts (main, sidebar, admin)
├── static/           # css, images, js
├── config.php        # site settings
├── functions.php     # helpers
├── index.php         # router entry point
└── sitemap.php       # sitemap generator
```

- **Pages** live in `content/pages/` as `.md`.  
  Example: `about.md` → `/about`
- **Collections** are folders under `content/collections/`.  
  Example: `content/collections/blog/hello-world.md` → `/blog/hello-world`
- **Partials** (`partials/`) are reusable chunks.  
- **Templates** (`templates/`) define page layouts.  
- **Routes** (`app/routes/`) handle how requests map to content.

---

## First Edits

1. Change the site name in `config.php`:

   ```php
   'site' => [
     'name' => 'My First NostalgiaPHP Site',
     'base_url' => '/',
     'timezone' => 'America/New_York',
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

   - Visit <http://localhost:8000/about>.

4. Add a new collection:  
   Edit `config.php` and add a block like:

   ```php
   'collections' => [
     'blog' => [
       'permalink' => '/blog/{slug}',
       'list_url'  => '/blog',
       'sort'      => ['date', 'desc'],
     ],
   ],
   ```

   Then add items under `content/collections/blog/`.

---

## Deployment

- **Apache**: use the included `.htaccess` for pretty URLs.
- **Nginx**: add `try_files $uri $uri/ /index.php?$query_string;`.
- **Permissions**: make sure directories are `755` and files are `644`.

---

✅ That’s it — edit Markdown, refresh the browser, and your site updates.
