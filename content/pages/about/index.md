---
title: About
description: This is a sample About page.
hero_title: Example Hero Title
hero_subtitle: This is a hero subtitle.
hero_image: /static/media/1.jpg
template: sidebar
---

<style>
  pre {
    max-width: 100% !important;
  }
</style>

# Why another CMS?

Because *modern web development* doesn’t have to be chaos.

NostalgiaPHP gives you the essentials — nothing more, nothing less — so you can just build.

## Client-side rendered gallery

```html
<script type="module" src="/static/js/apps/gallery.js"></script>

<div
  class="auto-fill"
  data-gallery="
    /static/media/1.jpg,
    /static/media/2.jpg,
    /static/media/3.jpg,
    /static/media/4.jpg,
    /static/media/5.jpg,
    /static/media/6.jpg
  "
></div>
```

<script type="module" src="/static/js/apps/gallery.js"></script>

<div style="padding-block: var(--size)">
  <div class="auto-fill" data-gallery="/static/media/1.jpg, /static/media/2.jpg, /static/media/3.jpg, /static/media/4.jpg, /static/media/5.jpg, /static/media/6.jpg"></div>
</div>
