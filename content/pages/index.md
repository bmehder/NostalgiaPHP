---
title: Welcome to NostalgiaPHP
description: This is the homepage description.
hero_title: Welcome to Nostalgia PHP
hero_subtitle: A caveman-simple file-based CMS
hero_image: /static/uploads/cave-water.jpg
layout: full-width
---

<style>
  section:nth-of-type(even) > .outer {
    background-color: #f9f9f9;
  }
</style>

<!-- Intro / value prop -->
<section class="section">
  <div class="outer">
    <div class="inner auto-fit">
      <div>
        <h2>Rock, Paper, Markdown</h2>
        <p class="lead" style="font-size:1.125rem;max-width:60ch">
          Build real content sites fast with flat files. Markdown in, HTML out.
          No framework. No database. Just pages, collections, and a few partials.
        </p>
        <div style="display:flex; gap:.75rem; flex-wrap:wrap; margin-top:1rem">
          <a class="btn" href="/dox/getting-started">Get Started</a>
          <a class="btn" href="/blog">View Blog</a>
          <a class="btn" href="https://github.com/bmehder/NostalgiaPHP" target="_blank" rel="noopener">GitHub</a>
        </div>
      </div>
      <figure>
        <img src="/static/uploads/2.jpg" alt="NostalgiaPHP project structure overview">
        <figcaption class="visually-hidden">Example project structure</figcaption>
      </figure>
    </div>
  </div>
</section>

<!-- Highlights / features -->
<section class="section">
  <div class="outer">
    <div class="inner">
      <div class="cards auto-fill">
        <article class="card bg-white">
          <div class="card-text">
            <h3 class="card-title">Files, not fuss</h3>
            <p class="card-excerpt">Pages and collections are just <code>.md</code> files with front-matter. Rename a file to change a URL. That’s it.</p>
          </div>
        </article>
        <article class="card bg-white">
          <div class="card-text">
            <h3 class="card-title">Templates &amp; partials</h3>
            <p class="card-excerpt">Small PHP templates wrap your content; partials handle header, footer, hero.</p>
          </div>
        </article>
        <article class="card bg-white">
          <div class="card-text">
            <h3 class="card-title">Portable content</h3>
            <p class="card-excerpt">Your “CMS” is Markdown. Move it anywhere—PHP today, something else tomorrow—no export dance.</p>
          </div>
        </article>
        <article class="card bg-white">
          <div class="card-text">
            <h3 class="card-title">Zero build step</h3>
            <p class="card-excerpt">No Node, no bundlers. Drop on a PHP host or run <code>php -S localhost:8000</code> and you’re live.</p>
          </div>
        </article>
      </div>
    </div>
  </div>
</section>

<!-- How it works (3 steps) -->
<section class="section">
  <div class="outer">
    <div class="inner auto-fit">
      <div>
        <h2>How it works</h2>
        <ol>
          <li><strong>Write content</strong> in <code>content/pages</code> and <code>content/collections/{name}</code>.</li>
          <li><strong>Add front-matter</strong> for <code>title</code>, <code>description</code>, <code>date</code> (optional).</li>
          <li><strong>Pick a layout</strong> via <code>layout: main</code> (or your own), and ship.</li>
        </ol>
      </div>
      <figure>
        <img src="/static/uploads/3.jpg" alt="NostalgiaPHP project structure overview">
        <figcaption class="visually-hidden">Example project structure</figcaption>
      </figure>
    </div>
  </div>
</section>

<!-- Recent posts (auto: just link to collection) -->
<section class="section">
  <div class="outer">
    <div class="inner">
      <h2>From the blog</h2>
      <p>Browse the latest posts and examples.</p>
      <p><a class="btn" href="/blog">See all posts →</a></p>
    </div>
  </div>
</section>