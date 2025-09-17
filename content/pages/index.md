---
title: Welcome to NostalgiaPHP
description: This is the homepage description.
hero_title: Welcome to Nostalgia PHP
hero_subtitle: A caveman-simple file-based CMS
hero_image: /static/uploads/cave-water.jpg
template: full-width
---

<style>
  section:nth-of-type(even) > .outer {
    background-color: var(--gray-100);
  }
</style>

<!-- Intro / value prop -->
<section class="section">
  <div class="outer">
    <div class="inner auto-fit">
      <div class="flow">
        <h2>Rock, Paper, Markdown</h2>
        <p class="lead">
          Build real content sites fast with flat files. Markdown in, HTML out.
          No framework. No database. Just pages, collections, and a few partials.
        </p>
        <div class="flex flex-wrap" style="gap: var(--size-0-5);">
          <a class="button" href="/dox/getting-started">Get Started</a>
          <a class="button" href="/blog">View Blog</a>
          <a class="button" href="https://github.com/bmehder/NostalgiaPHP" target="_blank" rel="noopener">GitHub</a>
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
      <h2>The Simple Superpowers</h2>
      <div class="cards auto-fill" style="margin-block-start: var(--size-3)">
        <article class="card bg-white">
          <div class="card-text">
            <h3 class="card-title">
            <span class="icon" aria-hidden="true">
              <svg aria-hidden="true" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V9z"/>
                <path d="M14 3v6h6"/>
                <path d="M9 13h6M9 17h6"/>
              </svg>
            </span>
            Files, no faff
            </h3>
            <p class="card-excerpt">Pages and collections are just <code>.md</code> files with front-matter. Rename a file to change a URL. That’s it.</p>
          </div>
        </article>
        <article class="card bg-white">
          <div class="card-text">
            <h3 class="card-title">
            <span class="icon" aria-hidden="true">
              <svg aria-hidden="true" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="4" width="18" height="6" rx="1.5"/>
                <rect x="3" y="10" width="18" height="6" rx="1.5"/>
                <rect x="3" y="16" width="18" height="4" rx="1.5"/>
              </svg>
            </span>
            Templates &amp; partials
            </h3>
            <p class="card-excerpt">Choose your template. Wrap your content; partials handle header, footer, hero, etc.</p>
          </div>
        </article>
        <article class="card bg-white">
          <div class="card-text">
            <h3 class="card-title">
            <span class="icon" aria-hidden="true">
              <svg aria-hidden="true" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 7l8-4 8 4-8 4-8-4z"/>
                <path d="M4 7v6l8 4 8-4V7"/>
                <path d="M12 11v7"/>
                <path d="M16 12l4 0-2 2-2-2z"/>
              </svg>
            </span>
            Portable content
            </h3>
            <p class="card-excerpt">Your “CMS” is Markdown. Move it anywhere—PHP today, something else tomorrow—no export dance.</p>
          </div>
        </article>
        <article class="card bg-white">
          <div class="card-text">
            <h3 class="card-title">
            <span class="icon" aria-hidden="true">
              <svg aria-hidden="true" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 7l8-4 8 4-8 4-8-4z"/>
                <path d="M4 7v6l8 4 8-4V7"/>
                <path d="M12 11v7"/>
                <path d="M16 12l4 0-2 2-2-2z"/>
              </svg>
            </span>
            Zero build step
            </h3>
            <p class="card-excerpt">No Node, no bundlers. Drop on a PHP host or run <code>php -S localhost:8000</code> and you’re live.</p>
          </div>
        </article>
        <article class="card bg-white">
          <div class="card-text">
            <h3 class="card-title">
            <span class="icon" aria-hidden="true">
              <svg aria-hidden="true" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="6" r="2"/>
                <circle cx="6" cy="18" r="2"/>
                <circle cx="18" cy="18" r="2"/>
                <path d="M12 8v4M12 12H6m6 0h6"/>
              </svg>
            </span>
            Instant sitemap
            </h3>
            <p class="card-excerpt"><code>sitemap.xml</code> is built automatically from your content—no plugins needed.</p>
          </div>
        </article>
        <article class="card bg-white">
          <div class="card-text">
            <h3 class="card-title">
            <span class="icon" aria-hidden="true">
              <svg aria-hidden="true" viewBox="0 0 24 24" width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 2v20"/>
                <path d="M5 6h10l2 2-2 2H5z"/>
                <path d="M19 14H9l-2 2 2 2h10z"/>
              </svg>
            </span>
            404, sorted
            </h3>
            <p class="card-excerpt">Bad links happen. A built-in <code>404.php</code> route is ready out of the box—or tweak it to fit your site.</p>
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
          <li><strong>Pick a template</strong> via <code>template: main</code> (or your own), and ship.</li>
        </ol>
        <a class="button" href="/dox/">View the Dox</a>
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
      <p><a class="button" href="/blog">See all posts →</a></p>
    </div>
  </div>
</section>
