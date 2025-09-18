---
title: About
description: This is a sample About page.
template: sidebar
---

# Why another CMS?

Because *modern web development* doesn’t have to be so crazy.

## Client-side rendered gallery

```html
<script type="module" src="/static/js/apps/gallery.js"></script>

<div
  class="auto-fill"
  data-gallery="
    /static/uploads/1.jpg,
    /static/uploads/2.jpg,
    /static/uploads/3.jpg,
    /static/uploads/4.jpg,
    /static/uploads/5.jpg,
    /static/uploads/6.jpg
  "
></div>
```

<script type="module" src="/static/js/apps/gallery.js"></script>

<div style="padding-block: var(--size)">
  <div class="auto-fill" data-gallery="/static/uploads/1.jpg, /static/uploads/2.jpg, /static/uploads/3.jpg, /static/uploads/4.jpg, /static/uploads/5.jpg, /static/uploads/6.jpg"></div>
</div>
