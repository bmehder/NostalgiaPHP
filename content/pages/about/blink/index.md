---
title: Blink Demo
description: Demo of the Blink (Slank) reactivity system.
draft: true
date: 2025-09-06
---

# Blink Demo

Here’s a little interactive block:

<style>
  .accordion [data-panel] {
    padding-block: var(--size-0-5);
    padding-inline: var(--size);
    border: 1px solid #ddd;
  }
</style>

<script type="module" src="/static/js/accordion.js"></script>

<!-- You can add multiple instances on the same page -->
<section class="flow">
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
</section>
