<?php include path('partials') . '/head.php'; ?>

<style>
  section:nth-of-type(even)>.outer {
    background-color: var(--stone-100);
  }
</style>

<!-- Appear.js -->
<script defer src="/static/js/apps/appear.js"></script>

<body class="home full-width">
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>
    <?= $hero_html ?>
    <main id="main">
      <!-- Intro / value prop -->
      <section>
        <div class="outer">
          <div class="inner">
            <div class="content auto-fit reverse">
              <div class="flow appear appear-right">
                <div class="kicker">Lightweight by design.</div>
                <h2>Rock, Paper, Markdown</h2>
                <p class="lead">
                  Build content sites fast with flat files.
                  No framework. No database. Just pages, collections, and a few partials.
                </p>
                <p>NostalgiaPHP creates clean HTML with zero build step—so you focus on words and layout, not
                  toolchains. Portable by default: zip it, copy it, deploy anywhere PHP runs.</p>
                <div class="flex flex-wrap" style="gap: var(--size-0-5);">
                  <a class="button" href="/dox/getting-started">Get Started</a>
                  <a class="button" href="/blog">View Blog</a>
                  <a class="button" href="https://github.com/bmehder/NostalgiaPHP" target="_blank"
                    rel="noopener">GitHub</a>
                </div>
              </div>
              <figure class="appear appear-left" style="margin-inline: auto;">
                <img class="landscape fit" src="/static/media/rock-paper-markdown.jpg"
                  alt="NostalgiaPHP project structure overview">
                <figcaption class="visually-hidden">Example project structure</figcaption>
              </figure>
            </div>
          </div>
        </div>
      </section>

      <!-- Highlights / features -->
      <section class="section">
        <div class="outer">
          <div class="inner">
            <div class="kicker">Back to basics</div>
            <h2>The Nostalgia Core</h2>
            <div class="cards auto-fill align-items-stretch appear appear-scale" style="margin-block-start: var(--size-3)">
              <?php
              include path('data') . '/features.php';
              foreach ($features as $feature):
                $title = $feature['title'];
                $excerpt = $feature['excerpt'];
                $icon = $feature['icon'];
                include path('partials') . '/feature-card.php';
              endforeach; ?>
            </div>
          </div>
        </div>
      </section>

      <!-- How it works (3 steps) -->
      <section class="section">
        <div class="outer">
          <div class="inner">
            <div class="content auto-fit reverse">
              <div class="flow appear appear-right">
                <div class="kicker">Under the hood, there’s barely a hood.</div>
                <h2>How It Works</h2>
                <ol>
                  <li><strong>Write content</strong> in <code>content/pages</code> and
                    <code>content/collections/{name}</code>.
                  </li>
                  <li><strong>Add front-matter</strong> like<code> title</code>, <code>description</code>, and
                    <code>date</code>.
                  </li>
                  <li><strong>Pick a template</strong> via <code>template: main</code> (or your own), and ship.</li>
                  <li><strong>Include partials</strong> for headers, footers, sidebars, or anything you want to reuse
                    across pages.</li>
                  <li><strong>Deploy anywhere PHP runs</strong>—no database, no build step, no lock-in.</li>
                </ol>
                <a class="button" href="/dox/">View the Dox</a>
              </div>
              <div class="appear appear-up">
                <pre>
                  <code class="language-yaml">
---
title: About NostalgiaPHP
description: A tiny, flat-file CMS for sites.
date: 2025-09-21
template: main
tags: intro, php, markdown
---
                  </code>
                  <code class="language-markdown">
# About NostalgiaPHP

No database. No build step. No framework.

It’s **Markdown in, HTML out**.
                  </code>
                </pre>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Recent posts (auto: just link to collection) -->
      <section class="section">
        <div class="outer">
          <div class="inner">
            <?php
            if (!isset($blog_items)) {
              $blog_items = array_slice(list_collection('blog') ?? [], 0, 3);
            }
            ?>
            <?php if (!empty($blog_items)): ?>
              <section class="from-blog appear appear-scale">
                <div class="kicker">Latest scribbles from the cave wall.</div>
                <h2>From the Blog</h2>
                <?php
                $items = $blog_items;
                $collection = 'blog';
                include path('partials') . '/cards-grid.php';
                ?>
                <div class="text-align-right full-width" style="margin-block-start: var(--size-1-5)"><a
                    href="<?= url('/blog') ?>">See all posts →</a></div>
              </section>
            <?php endif; ?>
          </div>
        </div>
      </section>
    </main>
    <?php include path('partials') . '/footer.php'; ?>
  </div>