---
title: Using Icons in NostalgiaPHP
description: A guide to the bundled SVG icons, how to use them, and where to find more.
date: 2025-09-17
author: NostalgiaPHP
draft: false
template: main
---

# Icons in NostalgiaPHP

Sometimes plain text just doesn’t cut it — icons help readers scan faster, add personality, and clarify meaning. NostalgiaPHP ships with a small **starter icon pack** you can use right away.

---

## Bundled Icons

These live in `/static/icons/`

- ![](/static/icons/home.svg) **Home**
- ![](/static/icons/search.svg) **Search**
- ![](/static/icons/file.svg) **File**
- ![](/static/icons/markdown.svg) **Markdown**
- ![](/static/icons/image.svg) **Image**
- ![](/static/icons/gallery.svg) **Gallery**
- ![](/static/icons/link.svg) **Link**
- ![](/static/icons/external-link.svg) **External Link**
- ![](/static/icons/user.svg) **User**
- ![](/static/icons/calendar.svg) **Calendar**
- ![](/static/icons/tag.svg) **Tag**

> Tip: these SVGs are tiny and styleable. Most use `stroke="currentColor"` so they inherit text color. The `markdown.svg` uses a filled badge with white glyphs for contrast.

---

## How to Use Icons

### 1) As images
```html
<img src="/static/icons/search.svg" alt="Search" />
```

### 2) Inline SVG via PHP include (inherits color)
```php
<span class="text-gray-600 flex align-items-center gap-2">
  <?php include 'static/icons/search.svg'; ?>
  Search
</span>
```

### 3) As background images (utility class)
```css
.icon { display:inline-block; width:1.25rem; height:1.25rem; vertical-align:middle; }
.icon-home { background: no-repeat center/contain url('/static/icons/home.svg'); }
```
```html
<i class="icon icon-home" aria-hidden="true"></i>
```

---

## Styling Tips

- **Color:** wrap inline SVG with a color class (e.g., `text-gray-600`) and it’ll inherit that color.
- **Alignment:** add `vertical-align: middle;` to icon class to align with text.
- **Accessibility:** if the icon is decorative, set `aria-hidden="true"` and empty `alt=""`. If it conveys meaning, include a concise `alt` or nearby text.

---

## Where These Icons Come From

We curated this set from excellent, MIT-licensed projects — perfect if you want more:

- **Lucide Icons** – clean, modern Feather fork: https://lucide.dev
- **Tabler Icons** – large, consistent set: https://tabler-icons.io
- **Simple Icons** – brand/tech logos (Markdown, GitHub, PHP): https://simpleicons.org

Copy additional SVGs into `/static/icons/` and use them the same way.

---

## Example: Icon List in a Card

```html
<ul>
  <li class="flex items-center gap-2">
    <?php include 'static/icons/file.svg'; ?>
    <span>Plain file</span>
  </li>
  <li class="flex items-center gap-2">
    <?php include 'static/icons/markdown.svg'; ?>
    <span>Markdown document</span>
  </li>
  <li class="flex items-center gap-2">
    <?php include 'static/icons/image.svg'; ?>
    <span>Image asset</span>
  </li>
</ul>
```

---

Happy icon-ing!
