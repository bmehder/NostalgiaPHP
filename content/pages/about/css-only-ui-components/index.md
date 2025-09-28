---
title: CSS-only UI Components
description: Demo of some css-only versions of UI elements that used to require JavaScript.
template: main
---

<style>
  main .content {
    display: grid;
  }
</style>

<h1>CSS-only UI Components</h1>

UI components that once seemed to require JavaScript—often built with frameworks like React—are now possible with nothing more than HTML and CSS.

The web platform has grown dramatically, with modern CSS introducing features like scroll snapping, the `<details>` element, and the native `<dialog>` API. These tools let us build interactive patterns that used to demand JavaScript, while keeping our code lighter and easier to maintain.

This isn’t about replacing JavaScript entirely, but about recognizing where the platform already gives us what we need. Every time we lean on native HTML and CSS instead of shipping extra JS, we get better performance, simpler code, and improved accessibility for free.

## Carousel

This carousel looks and feels like something you’d normally reach for JavaScript to build, but it’s powered entirely by modern CSS.

- **Scrolling & snapping** — The cards sit in a horizontal strip you can swipe or scroll through. CSS scroll snapping keeps each card neatly centered as you stop.
- **Navigation buttons —** Instead of custom JavaScript, the browser provides built-in scroll buttons you can style directly with CSS. They appear at the edges and let you step through smoothly.
- **Indicators (dots)** — Markers show where you are in the carousel, also handled through CSS pseudo-elements. No extra markup or scripting needed.
- **Responsive by default** — Because it’s flexbox + aspect ratio, the layout adapts gracefully to small or large screens.

In short: the browser’s layout and scrolling engine is doing all the work. We’re just layering on styles.

> **Note:** If your browser doesn’t support newer CSS features like `::scroll-button` and `::scroll-marker`, you may not see the carousel controls or indicators. That’s okay — the content is still fully accessible as a scrollable strip. This is an example of _progressive enhancement_, where modern browsers get the upgraded experience, while older ones still get a usable fallback.

<div class="carousel">
  <div class="card"><img src="https://picsum.photos/500/300?random=1" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=2" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=3" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=4" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=5" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=6" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=7" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=8" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=9" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=10" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=11" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=12" alt /></div>
</div>

<div style="height: var(--size-3)"></div>

## Accordion

Here’s a clean, JS-free accordion using native `<details>` with a shared name so only one section can be open at a time.

- **Semantic HTML:** `<details>` / `<summary>` communicates “disclosure” to browsers, screen readers, and keyboards.
- **Built-in UX:** Space/Enter toggles; Arrow keys move focus between summaries (browser-dependent).
- **Mutual exclusivity:** Giving all `<details>` the same `name` attribute turns them into a group—opening one closes the others.

<div class="inner full-width flow" style="--inner-padding-block: var(--size-2); max-inline-size: 65ch; margin-inline: 0">
  <div class="faqs">
    <details name="faq">
      <summary>What is NostalgiaPHP?</summary>
      <div>
        <p>A tiny flat-file CMS: Markdown in, HTML out—no DB, no build step.</p>
      </div>
    </details>
    <details name="faq">
      <summary>How do I add a page?</summary>
      <div>
        <p>Create <code>content/pages/your-page/index.md</code> with front-matter and content.</p>
      </div>
    </details>
    <details name="faq">
      <summary>How do collections work?</summary>
      <div>
        <p>Put items in <code>content/collections/{name}</code> as <code>.md</code> files (one per item).</p>
      </div>
    </details>
    <details name="faq">
      <summary>Can I use templates and partials?</summary>
      <div>
        <p>Yes—pick <code>template: main</code> (or your own), and reuse partials like header/footer.</p>
      </div>
    </details>
    <details name="faq">
      <summary>Where can I deploy?</summary>
      <div>
        <p>Anywhere PHP runs—shared hosting, Render, Netlify PHP adapters, etc.</p>
      </div>
    </details>
  </div>
</div>

<style>
	dialog {
		width: min(100% - 3rem, var(--sm));
		padding: 0;

    .inner {
      display: grid;
      gap: var(--size-1-5);
      align-items: center;
      padding: var(--size-3);
    }
  }
</style>

<dialog	id="modal">
  <div class="inner flow">
    <h3>I am a modal</h3>
    <div>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Repellat nulla ad nemo.</div>
    <form method="dialog">
      <button>Close</button>
    </form>
  </div>
</dialog>

## CSS Dialogs

The `<dialog>` element gives you a built-in way to create modals without JavaScript. Opening and closing is handled natively with `.showModal()` and `.close()` *(so, a little JS, but let's move on)*, and the browser automatically adds a dimmed backdrop behind the dialog.

This means you don’t need to wire up ARIA attributes, focus trapping, or overlay click handling yourself — it’s all provided by the platform. You can still style the dialog and its backdrop to fit your design.

>**Note:** Backdrop styling (`::backdrop`) is still inconsistent across browsers. Some support blur and custom colors, while others are limited. In this example, there is no styling added to the `::backdrop` psuedo-element.

<div class="inner full-width flow" style="--inner-padding-block: var(--size-2)">
  <div>
    <button onclick="modal.showModal()">Show Modal</button>
  </div>
</div>