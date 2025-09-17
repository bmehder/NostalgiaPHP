---
title: Fetch Example
description: Example of fetching and displaying data client-side.
date: 2025-09-13
---

# Fetch Example

Fetch data from a REST API, and display the results using [Blink](/about/blink).

_Note: State not persisted._

<script type="module" src="/static/js/apps/fetch-todos.js"></script>

<div class="todo-list">
  <menu>
    <li><button type="button" data-filter="all" aria-pressed="true">All</button></li>
    <li><button type="button" data-filter="active" aria-pressed="false">Active</button></li>
    <li><button type="button" data-filter="completed" aria-pressed="false">Completed</button></li>
  </menu>
  
  <ul class="todos no-padding list-style-none" data-todos></ul>
</div>
