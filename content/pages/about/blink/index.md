---
title: Blink Demo
description: Demo of the Blink (Slank) reactivity system.
date: 2025-09-06
---

# Blink (officially, Slank) Demo

**Blink** is a dead-simple reactive signal system for JavaScript — no tooling, no build steps, no opinions.

It gives you three primitives:

- explicit() – create manually updated reactive values
- implicit() – create derived values that automatically update
- fx() – create reactive effects that re-run when dependencies change

Learn more at on [NPM](https://www.npmjs.com/package/slank).

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
  <h2>Accordion Type Thing</h2>
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
  <h2>Counter</h2>
  <div class="counter">
    <button data-decrement>-</button>
    <button data-increment>+</button>
    <button data-reset>Reset</button>
    <p>Count (explicit state): <span data-counter-value></span></br>
    Doubled (implicit state): <span data-counter-doubled></span></p>
  </div>
</div>
