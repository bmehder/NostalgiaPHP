---
title: Fetch Example
description: Example of fetching and displaying data client-side.
date: 2025-09-13
---

# Fetching and rendering dynamic data

While NostalgiaPHP keeps things simple with flat files, you can still add client-side behavior where you need it.

This example fetches todos from the JSONPlaceholder API and displays them in a list. Each item can be marked complete or incomplete in the browser. No server-side code required.

State is managed with [Blink](/about/blink), a tiny reactive utility that tracks changes and re-renders only what’s needed. The result is a snappy, modern feel without pulling in a heavyweight framework.

It’s the same idea as the [gallery example](/about#gallery-demo): small, portable code that you can drop into any NostalgiaPHP project. You can even persist state to local or session storage if you want it to stick between visits.

_Note: State not persisted in this example._

<script type="module" src="/static/js/apps/fetch-todos.js"></script>

<div class="todo-list">
  <menu>
    <li><button type="button" data-filter="all" aria-pressed="true">All</button></li>
    <li><button type="button" data-filter="active" aria-pressed="false">Active</button></li>
    <li><button type="button" data-filter="completed" aria-pressed="false">Completed</button></li>
  </menu>
  
  <ul class="todos no-padding list-style-none" data-todos></ul>
</div>
