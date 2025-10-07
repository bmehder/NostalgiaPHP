---
title: Browser Components
description: Demo of some css-only versions of UI elements that used to require JavaScript.
template: main
---

<style>
  main .content {
    display: grid;
    p + p {
      margin-block-end: var(--size-1-5);
    }
  }
</style>

<h1>CSS-only* Browser Components</h1>

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

<div style="min-height: var(--size-1-5)"></div>

> **Note:** If your browser doesn’t support newer CSS features like `::scroll-button` and `::scroll-marker`, you may not see the carousel controls or indicators. That’s okay — the content is still fully accessible as a scrollable strip. This is an example of _progressive enhancement_, where modern browsers get the upgraded experience, while older ones still get a usable fallback.

<div style="min-height: var(--size-1-5)"></div>

<div class="carousel">
  <div class="card"><img src="https://picsum.photos/500/300?random=1" width="500" height="300" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=2" width="500" height="300" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=3" width="500" height="300" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=4" width="500" height="300" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=5" width="500" height="300" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=6" width="500" height="300" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=7" width="500" height="300" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=8" width="500" height="300" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=9" width="500" height="300" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=10" width="500" height="300" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=11" width="500" height="300" alt /></div>
  <div class="card"><img src="https://picsum.photos/500/300?random=12" width="500" height="300" alt /></div>
</div>

<div style="height: var(--size-3)"></div>

## Accordion

This accordion is JS-free, built with native `<details>` and a shared name to ensure only one section is open at a time.

- **Semantic HTML:** `<details>` / `<summary>` communicates “disclosure” to browsers, screen readers, and keyboards.
- **Built-in UX:** Space/Enter toggles; the tab key moves focus between summaries (browser-dependent).
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

## Tabs

Tabs let you organize related content into panels where only one is visible at a time. Traditionally, this required JavaScript to toggle states and hide/show panels. But with plain HTML and CSS, we can achieve the same behavior using radio inputs and labels.

Each tab is backed by a hidden radio input (so only one can be active at a time), and the labels act as the clickable tab headers. CSS then uses the `:checked` state to display the correct panel.

This approach is accessible by default (since radios are part of the native form controls), keyboard-friendly, and requires no scripting. It’s also easy to style so the tabs look like the traditional UI pattern users expect.

<div class="inner full-width flow" style="--inner-padding-block: var(--size-2)">
  <div class="tabs" role="tablist" aria-label="Demo tabs">
    <input type="radio" name="tabset" id="tab-1" checked>
    <label for="tab-1">Overview</label>
    <input type="radio" name="tabset" id="tab-2">
    <label for="tab-2">Features</label>
    <input type="radio" name="tabset" id="tab-3">
    <label for="tab-3">Pricing</label>
    <div class="tab-panels">
      <section id="panel-1" role="tabpanel" aria-labelledby="tab-1">
        <h3>Overview</h3>
        <p>This is the overview panel.</p>
      </section>
      <section id="panel-2" role="tabpanel" aria-labelledby="tab-2">
        <h3>Features</h3>
        <p>This is the features panel.</p>
      </section>
      <section id="panel-3" role="tabpanel" aria-labelledby="tab-3">
        <h3>Pricing</h3>
        <p>This is the pricing panel.</p>
      </section>
    </div>
  </div>
</div>

## CSS Dialogs

<dialog	id="modal" class="modal">
  <div class="inner">
    <h3>I am a modal</h3>
    <div>Lorem ipsum dolor, sit amet consectetur adipisicing elit. Repellat nulla ad nemo.</div>
    <form method="dialog">
      <button>Close</button>
    </form>
  </div>
</dialog>

The `<dialog>` element gives you a built-in way to create modals without JavaScript. Opening and closing is handled natively with `.showModal()` and `.close()` *(so, a little JS, but let's move on)*, and the browser automatically adds a dimmed backdrop behind the dialog.

This means you don’t need to wire up ARIA attributes, focus trapping, or overlay click handling yourself — it’s all provided by the platform. You can still style the dialog and its backdrop to fit your design.

>**Note:** Backdrop styling (via `::backdrop`) is still inconsistent across browsers. Some support blur and custom colors, while others are limited.
>Not progressively enhanced.

<div class="inner full-width flow" style="--inner-padding-block: var(--size-2)">
  <div>
    <button onclick="modal.showModal()">Show Modal</button>
  </div>
</div>

## Before/After Comparison

Another classic UI pattern that used to almost always rely on heavy JavaScript libraries is the before–after slider. In this version, CSS handles the actual reveal effect using `clip-path` and a custom property. JavaScript plays only a minimal role: wiring up the range input so its value updates the CSS variable. So, it is not progressively enhanced.

It’s a much lighter approach than older libraries — most of the work is done natively in CSS, while JS just provides the bridge between user input and styling.

<script type="module" src="/static/js/apps/comparison.js"></script>

<div class="inner full-width flow" style="--inner-padding-block: var(--size-2)">
  <div class="comparison" data-comparison>
    <img class="before" src="https://picsum.photos/id/1015/960/640" width="960" height="640" alt="Before">
    <img class="after" src="https://picsum.photos/id/1016/960/640"  width="960" height="640"alt="After">
    <input type="range" min="0" max="100" value="50" aria-label="Image comparison slider">
  </div>
</div>