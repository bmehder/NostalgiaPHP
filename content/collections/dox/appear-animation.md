---
title: Appear Animations
description: A lightweight, JS-assisted approach to scroll-based reveal animations using IntersectionObserver and CSS transitions.
date: 2025-10-02
image: static/media/appear.jpg
template: main
tags: css, animation, intersectionobserver, progressive-enhancement
---

# Appear Animations

Subtle scroll-based animations are a nice way to make a page feel alive without pulling in a large library like GSAP. We can build a lightweight **“appear” helper** by combining:

- **IntersectionObserver** → detect when elements enter the viewport.
- **CSS transitions** → handle the actual fade/slide/scale animations.
- **A simple `.is-visible` toggle** → added once, no reflows.

This balances performance and user experience while respecting `prefers-reduced-motion`.

---

## CSS

Here’s the minimal CSS that defines hidden and visible states:

```css
@media (prefers-reduced-motion: reduce) {
  .appear,
  [data-appear-children] > * {
    transition: none !important;
    transform: none !important;
    opacity: 1 !important;
  }
}

.appear,
[data-appear-children] > * {
  opacity: 0;
  transform: translateY(10px);
  transition: opacity .45s ease, transform .45s ease;
  will-change: opacity, transform;
}

.is-visible,
[data-appear-children].is-visible > * {
  opacity: 1;
  transform: none;
}
```

- Single elements: add the .appear class.
- Containers: add data-appear-children to stagger-fade their children.

---

## JavaScript

A single script observes targets and applies .is-visible when needed:

```js
;(() => {
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches
  if (prefersReduced) return

  const STAGGER_DEFAULT = 70 // ms per child

  const onEnter = el => {
    if (el.hasAttribute('data-appear-children')) {
      const step = parseInt(el.getAttribute('data-stagger') || STAGGER_DEFAULT, 10)
      ;[...el.children].forEach((child, i) => {
        child.style.transitionDelay = `${i * step}ms`
      })
    }
    el.classList.add('is-visible')
  }

  const io = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        onEnter(entry.target)
        io.unobserve(entry.target)
      }
    })
  }, { threshold: 0.2 })

  document.querySelectorAll('.appear, [data-appear-children]').forEach(el => io.observe(el))
})()
```
---

## Usage

```html
<h2 class="appear">Fade In Heading</h2>

<div data-appear-children data-stagger="120">
  <p>First item</p>
  <p>Second item</p>
  <p>Third item</p>
</div>
```
---

## Why not GSAP?

GSAP is powerful, but often overkill for small sites. This approach is progressive enhancement:
- No JS → content still loads, just without animation.
- With JS → smooth, performant scroll-based reveals.

---

✨ That’s all it takes: a sprinkle of CSS + IntersectionObserver.