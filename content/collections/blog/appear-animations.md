---
title: Appear Animations
description: A lightweight, JS-assisted approach to scroll-based reveal animations using IntersectionObserver and CSS transitions.
date: 2025-10-02
image: static/media/appear.jpg
template: main
tags: css, animation, intersectionobserver
---

# Appear Animations

Subtle scroll-based animations can make a page feel alive without pulling in a large library like GSAP. We can build a lightweight **“appear” helper** by combining:

- **IntersectionObserver** → detect when elements enter the viewport.  
- **CSS transitions** → handle the actual fade/slide/scale animations.  
- **A simple `.is-visible` toggle** → applied once, no reflows.  

This balances performance and user experience while respecting `prefers-reduced-motion`.

---

## CSS

Instead of hardcoding hidden/visible styles globally, we can keep things safe: if the JavaScript never loads, elements remain visible. The script injects the CSS rules dynamically.

```css
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

---

## JavaScript

The script observes `.appear` elements and applies `.is-visible` when they enter the viewport:

```js
	// No IO support → reveal immediately (again: no hiding CSS injected).
const revealAllNow = () => {
	const show = () => {
		document
			.querySelectorAll('.appear')
			.forEach(el => el.classList.add('is-visible'))
	}
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', show, { once: true })
	} else {
		show()
	}
}

// Respect reduced motion → don't inject the hiding CSS; just reveal.
if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
	revealAllNow()
} else if (!('IntersectionObserver' in window)) {
	// No IO support → reveal immediately (again: no hiding CSS injected).
	revealAllNow()
} else {
	// JS present → inject CSS (so elements can start hidden) and observe
	injectCSS()

	const onIntersect = entries => {
		entries.forEach(entry => {
			if (entry.isIntersecting) {
				entry.target.classList.add('is-visible')
				io.unobserve(entry.target)
			}
		})
	}

	const io = new IntersectionObserver(onIntersect, { threshold: 0.2 })

	const start = () => {
		const targets = document.querySelectorAll('.appear')
		if (!targets.length) return
		targets.forEach(el => io.observe(el))
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start, { once: true })
	} else {
		start()
	}
}
```

---

## Usage

```html
<!-- Inlcude in <head> or template, etc. -->
<script defer src="/static/js/apps/appear.js"></script>

<h2 class="appear appear-up">Fade In Heading</h2>

<p class="appear appear-scale">This paragraph scales up slightly as it fades in.</p>

<div class="appear appear-left">Slide from left</div>
<div class="appear appear-right">Slide from right</div>
```

---

## Progressive Enhancement

⚠️ **JavaScript is required for this effect.**  
If the script doesn’t load, `.appear` elements will remain visible by default. The animations are a progressive enhancement — the page works fine without them.

---

✨ That’s all it takes: a sprinkle of CSS + IntersectionObserver for lightweight appear animations.
