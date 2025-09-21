---
title: About
description: This is a sample About page.
hero_title: About Nostalgia PHP
hero_subtitle: Files good. Framework bad.
hero_image: /static/media/about-nostalgia-php.jpg
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

For example, here’s a simple client-side image gallery with a lightbox. The JavaScript is completely standalone, and the CSS lives in its own file. Together they’re easy to reuse, drop into other projects, and move between sites with zero external dependencies.

<span id="gallery-demo"></span>

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
