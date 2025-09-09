---
title: NostalgiaPHP — Developer Guide
description: A guide to NostalgiaPHP for developers that is almost certiainly out of date most of the time.
slug: nostalgiaphp-developer-guide
date: 2025-09-06
draft: true
excerpt: This is an excerpt.
---

# NostalgiaPHP — Developer Guide (for WP/PHP folks)

## Quickstart
# run locally (PHP 8+)
`php -S localhost:8000`
# then visit http://localhost:8000

## Add a New Page
1. Create `content/pages/contact.md`
2. Add front‑matter:
   ```md
   ---
   title: Contact
   ---
   ## Say hello
   Email us at hello@example.com.
   ```
3. Visit `/contact`

## Add a New Collection
1. In `config.php` add:
   ```php
   'collections' => [
     'blog' => [ 'permalink' => '/blog/{slug}', 'list_url' => '/blog', 'sort' => ['date','desc'] ],
     'projects' => [ 'permalink' => '/projects/{slug}', 'list_url' => '/projects', 'sort' => ['date','desc'] ],
   ],
   ```
2. Create folder: `content/collections/projects/`
3. Add an item: `content/collections/projects/first-project.md`
   ```md
   ---
   title: First Project
   date: 2025-09-06
   ---
   Project body in **Markdown**.
   ```
4. Visit `/projects` (list) and `/projects/first-project` (item).

## Change the Navigation
Edit `partials/header.php` and add another `<a>` tag. No database, no menus UI — just HTML.

## Common Customizations
### 1) Real Markdown Parser
Drop `Parsedown.php` into the project and swap `markdown_to_html()` to use it:
```php
require_once __DIR__ . '/Parsedown.php';
$p = new Parsedown();
return $p->text($md);
```

### 2) Drafts
Hide items from lists when `draft: true`:
```php
// inside list_collection(), filter $items where meta['draft'] !== true
$items = array_filter($items, fn($it) => empty($it['meta']['draft']));
```

### 3) Excerpts
Add `excerpt:` in front‑matter and show it on collection list pages.

### 4) RSS for a Collection (sketch)
Create `/rss.php` that reads `list_collection('blog')`, renders an XML feed, and link to it in header/footer.

## Security Notes
- Content is trusted; still, we do `htmlspecialchars()` for titles and links.
- If you ingest user input, sanitize aggressively and disable raw HTML in Markdown.

## Nginx Example
```
location / {
  try_files $uri $uri/ /index.php;
}
```

## WordPress Mapping
- **Theme files** → `templates/` + `partials/`
- **The Loop** → `list_collection()` + foreach
- **Template Tags** → helpers in `functions.php`
- **Permalinks** → `.htaccess` (Apache) or `try_files` (Nginx)
- **Custom Fields** → front‑matter keys

## Style Guide (Tiny)
- Keep helpers in `functions.php` small and pure where possible.
- Prefer configuration in `config.php` over conditionals in templates.
- Keep `index.php` dumb (routing only).

## Testing Tips
- Manual: create pages/items and verify routes `/`, `/about`, `/blog`, `/blog/slug`.
- Automated (optional): write small PHPUnit tests for front‑matter parsing and collection sorting.
