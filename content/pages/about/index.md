---
title: About
description: NostalgiaPHP gives you the essentials — nothing more, nothing less — so you can just build.
hero_title: About Nostalgia PHP
hero_subtitle: Files good. Framework bad.
hero_image: /static/media/about-nostalgia-php.webp
hero_button_text: View Dox
hero_button_link: /dox
template: sidebar
---

<style>
  pre {
    max-width: 100% !important;
  }
</style>

# Why another CMS?

Because _modern web development_ doesn’t have to be chaos.

NostalgiaPHP gives you the essentials — nothing more, nothing less — so you can just build.

For example, here’s a simple client-side image gallery with a lightbox. The JavaScript is completely standalone, and the CSS lives in its own file. Together they’re easy to reuse, drop into other projects, and move between sites with zero external dependencies.

<span id="gallery-demo"></span>

## Client-side rendered gallery

<script type="module" src="/static/js/apps/gallery.js"></script>

<div class="auto-fill" style="--auto-fill-gap: var(--size-1-5)" data-gallery="/static/media/1.jpg, /static/media/2.jpg, /static/media/3.jpg, /static/media/4.jpg, /static/media/5.jpg, /static/media/6.jpg"></div>

### Gallery Code

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
