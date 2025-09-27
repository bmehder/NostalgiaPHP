---
title: Introducing the NostalgiaPHP JSON API
description: A minimal REST-style API for your flat-file content.
date: 2025-09-27
image: static/media/rest.jpg
tags: api, json, rest, nostalgia
---

# Introducing the NostalgiaPHP JSON API

NostalgiaPHP has always been about simplicity: flat files, partial templates, and portable sites. Now there’s a new way to consume your content — a minimal **JSON API** that makes your data available for JavaScript frontends, React components, mobile apps, or anything else that speaks HTTP.

## Available Routes

The API lives under `/api`. Here are the routes currently available:

* **`/api/health`** – quick check that the API is alive.
* **`/api/items`** – list items from all collections.
* **`/api/items/blog`** – list items from a specific collection.
* **`/api/pages`** – list all top-level pages.
* **`/api/pages/{slug}`** – get a single page by slug.
* **`/api/tags`** – list all tags across collections.
* **`/api/tags/{tag}`** – list all items filtered by a tag.

Responses are JSON, sorted by date (newest first when applicable), and include:

```json
{
  "ok": true,
  "count": 3,
  "items": [
    {
      "collection": "blog",
      "slug": "nostalgia-manifesto",
      "url": "/blog/nostalgia-manifesto",
      "title": "The NostalgiaPHP Manifesto",
      "date": "2025-09-12",
      "tags": ["intro", "php"],
      "html": "<p>Rendered HTML here...</p>"
    }
  ]
}
```

## CORS Allowlist

By default, browsers enforce [CORS](https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS) rules when your frontend requests data from the API. NostalgiaPHP supports a configurable allowlist so you can explicitly permit which origins are allowed to fetch API data.

In your `config.php`, set it like this:

```php
'api' => [
  'enabled' => true,
  'cors_allowlist' => [
    'https://svelte.dev',
    'https://nostalgiaphp.onrender.com',
    // 'http://localhost:5173', // enable for local dev if needed
  ],
],
```

This means only requests from those domains will receive the `Access-Control-Allow-Origin` header and succeed in the browser.

## Why It Matters

Instead of scraping HTML or duplicating content, you can:

* Build a Svelte/React/Vue frontend that consumes your flat-file content.
* Create widgets that fetch recent blog posts.
* Use the API as a lightweight backend for apps.

It’s REST-flavored, but intentionally minimal: GET requests only, JSON out. The rest is up to you.

---

🚀 Try it now: spin up `php -S localhost:8000` and visit [http://localhost:8000/api](http://localhost:8000/api).
