---
title: Sample Page
description: One-line summary for SEO/snippets.
draft: true
date: 2025-09-06
---

# Sample Page

This is a sample page.

# Demo Blink

Here’s a little interactive block:

<script type="module" src="/static/js/sample-inline.js"></script>

<style>
  /* Optional minimal styling (safe to inline in MD) */
  .accordion { margin-block: 1rem; }
  .accordion [data-panel] { padding: .75rem 1rem; border: 1px solid #ddd; border-radius: .5rem; }
  .accordion [data-toggle] { cursor: pointer; }
</style>

<!-- You can add multiple instances on the same page -->
<div class="accordion" data-scope>
  <button data-toggle aria-expanded="false">Show more</button>
  <div data-panel hidden>
    <p>Second instance. Independent state.</p>
  </div>
</div>