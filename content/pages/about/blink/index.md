---
title: Blink Reactivity
description: Demo of the Blink (Slank) reactivity system.
date: 2025-09-06
---

# Blink Reactivity

**Blink** is a dead-simple reactive signal system for JavaScript — no tooling, no build steps, no opinions.

Blink gives you three primitives:

- explicit() – to create state that is manually updated
- implicit() – to create state derived from explicit state
- fx() – to create side effects that re-run when state changes

Learn more at [NPM](https://www.npmjs.com/package/slank).


> **Mental Model for Any Reactive UI Library (React, Vue, Svelte, Blink, etc.):**
>
> library eats state → view comes out
>
> ```
> library(📦) → 💩👀
> ```
>
> The view is always the result of applying the library to the state — in *real time*.  
> 
> **Change the state → the view reacts to the change.**
>
> (state → library → view)

<style>
  main {
    .inner {
      --inner-padding-block: var(--size);
    }
  }

  .accordion [data-panel] {
    padding-block: var(--size-0-5);
    padding-inline: var(--size);
  }
</style>

<script type="module" src="/static/js/accordion.js"></script>

<!-- You can add multiple instances on the same page -->

<div class="flow">
  <h2>Demos</h2>
  <h3>Accordion<em>-ish</em> Thing</h3>
  <p>Uses explicit state <code>isOpen</code>.</p>
  <div class="accordion" data-scope>
    <button data-toggle aria-expanded="false"></button>
    <div data-panel hidden>
      <p>First instance. Independent state.</p>
    </div>
  </div>
  <div class="accordion" data-scope>
    <button data-toggle aria-expanded="false"></button>
    <div data-panel hidden>
      <p>Second instance. Independent state.</p>
    </div>
  </div>
  <div class="accordion" data-scope>
    <button data-toggle aria-expanded="false"></button>
    <div data-panel hidden>
      <p>Third instance. Independent state.</p>
    </div>
  </div>
</div>

<script type="module" src="/static/js/counter.js"></script>

<div class="flow">
  <h3>Counter</h3>
  <p>Uses explicit state <code>count</code> and implicit state <code>doubled</code>.</p>

  <div class="counter">
    <button data-decrement>-</button>
    <button data-increment>+</button>
    <button data-reset>Reset</button>
    <p>Count: <span data-counter-value></span></br>
    Doubled: <span data-counter-doubled></span></p>
  </div>
</div>
