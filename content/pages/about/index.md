---
title: About
description: This is a sample About page.
hero_title: About Nostalgia PHP
hero_subtitle: A subtitle.
hero_image: /static/uploads/1.jpg
template: sidebar
---

# Why another CMS?

Because *modern web development* doesn’t have to be so stupid.

Here are some buttons. They are just here.

<menu class="flex flex-wrap gap-0-2-5 no-padding list-style-none">
  <li><a class="button" href="/about/blink">Blink</a></li>
  <li><a class="button" href="/about/blank">Blank</a></li>
  <li><a class="button" href="/about/fetch">Fetch</a></li>
</menu>

## Client-side rendered gallery

```html
<script type="module" src="/static/js/apps/gallery.js"></script>

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

<script type="module" src="/static/js/apps/gallery.js"></script>

<div class="auto-fill" data-gallery data-images="/uploads/1.jpg, /uploads/2.jpg, /uploads/3.jpg, /uploads/4.jpg, /uploads/5.jpg, /uploads/6.jpg"></div>

