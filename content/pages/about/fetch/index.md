---
title: Fetch Example
description: Example of fetching and displaying data client-side.
date: 2025-09-13
---

# Fetch Example

Fetch data from a REST API, and display the results using [Blink](/about/blink).

*Note: State not persisted.*

<script type="module" src="/static/js/fetch-todos.js"></script>

<nav class="data-filters" data-filters>
  <button type="button" data-filter="all" aria-pressed="true">All</button>
  <button type="button" data-filter="active" aria-pressed="false">Active</button>
  <button type="button" data-filter="completed" aria-pressed="false">Completed</button>
</nav>

<ul class="todos" data-todos></ul>