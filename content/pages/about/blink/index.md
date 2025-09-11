---
title: Blink Demo
description: Demo of the Blink (Slank) reactivity system.
date: 2025-09-06
---

# Blink Demo

Here’s a little interactive block:

<style>
  .inner {
    --inner-padding-block: var(--size);
  }

  .accordion [data-panel] {
    padding-block: var(--size-0-5);
    padding-inline: var(--size);
  }
</style>

<script type="module" src="/static/js/accordion.js"></script>

<!-- You can add multiple instances on the same page -->
<section>
  <div class="outer">
    <div class="inner flow">
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
  </div>
</section>

<script type="module" src="/static/js/counter.js"></script>

<section>
  <div class="outer">
    <div class="inner flow">
      <h2>Counter</h2>
      <div class="counter">
        <button data-decrement>-</button>
        <button data-increment>+</button>
        <button data-reset>Reset</button>
        <p>Count (explicit state): <span data-counter-value></span></p>
        <p>Doubled (implicit state): <span data-counter-doubled></span></p>
      </div>
    </div>
  </div>
</section>
