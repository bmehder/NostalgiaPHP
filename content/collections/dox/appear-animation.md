---
title: Appear Animations
description: A lightweight scroll-based reveal animation technique using IntersectionObserver and CSS transitions.
date: 2025-10-02
image: static/media/appear.jpg
template: main
tags: css, animation, intersectionobserver
---

# Appear Animations

Scroll-based animations can add a subtle sense of depth and polish to a site.  
Instead of reaching for a full animation library like GSAP, we can achieve a **lightweight “appear” effect** by combining:

- **IntersectionObserver** → detect when elements enter the viewport.  
- **CSS transitions** → fade, slide, or scale into place.  
- **A simple `.is-visible` toggle** → applied only once.  

This keeps things small, performant, and easy to extend.

---

## CSS

We start with a base hidden state and a visible state:

```css
/* Base hidden state */
.appear {
	--appear-translate: 128px;

	opacity: 0;
	transform: translateY(20px);
	transition: opacity 0.5s ease, transform 0.5s ease;
	will-change: opacity, transform;

	/* appeared */
	&.is-visible {
		opacity: 1;
		transform: none;
	}
}

/* Variants override the baseline transform */
.appear-up {
	transform: translateY(var(--appear-translate));
}
.appear-down {
	transform: translateY(calc(var(--appear-translate) * -1));
}
.appear-left {
	transform: translateX(var(--appear-translate));
}
.appear-right {
	transform: translateX(calc(var(--appear-translate) * -1));
}
.appear-scale {
	transform: scale(0.95);
}
```

- Add .appear for a simple fade+slide.
- Add a variant (e.g. .appear-left) to change the motion.

---

## JavaScript

A small script observes .appear elements and reveals them on scroll:

```js
;(() => {
  if (!('IntersectionObserver' in window)) {
    document.querySelectorAll('.appear')
      .forEach(el => el.classList.add('is-visible'))
    return
  }

  const io = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('is-visible')
        io.unobserve(entry.target)
      }
    })
  }, { threshold: 0.2 })

  document.querySelectorAll('.appear').forEach(el => io.observe(el))
})()
```

---

## Usage

```html
<h2 class="appear">Fade In Heading</h2>
<p class="appear appear-left">Slide In from Left</p>
<p class="appear appear-scale">Subtle Scale-Up</p>
```

---

## ⚠️ A Note on JavaScript Requirement

This technique **requires JavaScript**.

If the script is removed or fails to load, elements with `.appear` will remain invisible because their CSS starts in a hidden state.

If you want a more robust, progressive enhancement pattern (where the content always shows, even without JS), you’d need to invert the approach—for example by showing elements by default and only hiding them after JS confirms it’s available.

Right now, this is just a proof of concept.

---

## Why not GSAP?

GSAP is fantastic for complex timelines, physics, or highly choreographed sequences.

But for simple “elements fade/slide into view” effects, this vanilla approach is:

- Tiny → no dependencies.
- Performant → IntersectionObserver is native and efficient.
- Flexible → extendable with just a few extra CSS rules.

---

✨ That’s it: a sprinkle of CSS + a tiny script gives you clean, reusable appear animations.