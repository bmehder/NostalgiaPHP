<?php include path('partials') . '/head.php'; ?>

<?php
$features = [
  [
    'title' => 'Files, no faff',
    'excerpt' => 'Pages and collections are just <code>.md</code> files with front-matter. Rename a file to change a URL.
                That’s it.',
    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                  stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                  <path d="M14 2v6h6" />
                  <path d="M16 13H8" />
                  <path d="M16 17H8" />
                </svg>',
  ],
  [
    'title' => 'Templates & partials',
    'excerpt' => 'Choose your template. Wrap your content; partials handle header, footer, hero, etc.',
    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <rect x="3" y="3" width="7" height="7" />
                        <rect x="14" y="3" width="7" height="7" />
                        <rect x="14" y="14" width="7" height="7" />
                        <rect x="3" y="14" width="7" height="7" />
                      </svg>',
  ],
  [
    'title' => 'Portable content',
    'excerpt' => 'Your “CMS” is Markdown. Move it anywhere—PHP today, something else tomorrow—no export dance.',
    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path d="M16.5 9.4L7.5 4.2" />
                        <path
                          d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" />
                        <path d="M3.3 7l8.7 5v10" />
                        <path d="M20.7 7l-8.7 5" />
                      </svg>',
  ],
  [
    'title' => 'Zero build step',
    'excerpt' => 'No Node, no bundlers. Drop on a PHP host or run <code>php -S localhost:8000</code> and you’re live.',
    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                      </svg>',
  ],
  [
    'title' => 'Instant sitemap',
    'excerpt' => '<code>sitemap.xml</code> is built automatically from your content—no plugins needed.',
    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <circle cx="12" cy="12" r="4" />
                        <line x1="12" y1="2" x2="12" y2="8" />
                        <line x1="12" y1="16" x2="12" y2="22" />
                        <line x1="2" y1="12" x2="8" y2="12" />
                        <line x1="16" y1="12" x2="22" y2="12" />
                      </svg>',
  ],
  [
    'title' => '404, sorted',
    'excerpt' => 'Bad links happen. A built-in <code>404.php</code> route is ready out of the box—or tweak it to fit your site.',
    'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                        <path d="M12 2v20" />
                        <path d="M18 8H6l-2-2 2-2h12l2 2-2 2z" />
                        <path d="M6 16h12l2 2-2 2H6l-2-2 2-2z" />
                      </svg>',
  ],
];
?>

<style>
  section:nth-of-type(even)>.outer {
    background-color: var(--stone-100);
  }
</style>

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
              <div class="flow">
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
              <figure style="margin-inline: auto;">
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
            <div class="cards auto-fill align-items-stretch" style="margin-block-start: var(--size-3)">
              <?php
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
              <div class=" flow">
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
              <pre><code class="language-yaml">---
title: About NostalgiaPHP
description: A tiny, flat-file CMS for sites.
date: 2025-09-21
template: main
tags: intro, php, markdown
---
              </code><code class="language-markdown">
# About NostalgiaPHP

No database. No build step. No framework.

It’s **Markdown in, HTML out**.
              </code></pre>
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
              <section class="from-blog">
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