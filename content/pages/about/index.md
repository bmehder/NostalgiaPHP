---
title: About
description: This is a sample About page.
hero_title: About Nostalgia PHP
hero_subtitle: A subtitle.
hero_image: /static/uploads/1.jpg
layout: sidebar
---

# Why another CMS?

Because *modern web development* doesn’t have to be so stupid.

<a href="/about/blink" class="button">Blink</a>
<a href="/about/blank" class="button">Blank</a>
<a href="/about/fetch" class="button">Fetch</a>

## Client-side rendered gallery

```html
<script type="module" src="/static/js/gallery.js"></script>

<div
  class="auto-fill"
  data-gallery
  data-images="
    /uploads/1.jpg,
    /uploads/2.jpg,
    /uploads/3.jpg,
    /uploads/4.jpg,
    /uploads/5.jpg,
    /uploads/6.jpg
  "
></div>
```

<script type="module" src="/static/js/gallery.js"></script>

<div class="auto-fill" data-gallery data-images="/uploads/1.jpg, /uploads/2.jpg, /uploads/3.jpg, /uploads/4.jpg, /uploads/5.jpg, /uploads/6.jpg"></div>

