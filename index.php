<?php
// Dev-only: prevent HTML caching
if (!headers_sent()) {
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  header('Pragma: no-cache');
  header('Expires: 0');
}

ini_set('display_errors', 'On');

$cfg = require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';

date_default_timezone_set(site('timezone') ?: 'UTC');

$path = trim(request_path(), '/');
$parts = $path === '' ? [] : explode('/', $path);
$first = $parts[0] ?? '';

$hero_html = null;

require __DIR__ . '/sitemap.php';

if ($path === 'robots.txt') {
  header('Content-Type: text/plain; charset=utf-8');
  echo "User-agent: *\n";
  echo "Disallow: /*.md$\n";   // prevent crawling raw markdown files
  echo "\n";
  echo "Sitemap: " . url('/sitemap.xml') . "\n";
  exit;
}

/**
 * Local helper: render a heading + card grid for a list of items.
 * $collectionContext is used when items don't carry their collection (e.g., list_collection()).
 */
$render_cards = function (array $items, $heading = null, $empty_msg = 'No items yet.', $collectionContext = null) {
  ob_start();
  if ($heading) {
    echo '<h1>' . htmlspecialchars($heading) . '</h1>';
  }
  if (!$items) {
    echo '<p>' . htmlspecialchars($empty_msg) . '</p>';
  } else {
    echo '<div class="cards auto-fill">';
    foreach ($items as $it) {
      // Prefer explicit context, otherwise look for metadata hints
      $collection = $collectionContext
        ?? ($it['meta']['_collection'] ?? ($it['collection'] ?? ''));
      $item = $it;
      include path('partials') . '/card.php';
    }
    echo '</div>';
  }
  return ob_get_clean();
};

// ---------- Home route ----------
if ($path === '') {
  $page = load_page('index');

  if (!$page) {
    http_response_code(404);
    $title = 'Not Found';
    $content = '<p>Create content/pages/index.md</p>';
    $meta = [];
    $hero_html = '';
  } else {
    $meta = $page['meta'] ?? [];
    $title = $meta['title'] ?? site('name');
    $content = $page['html']; // just the markdown HTML

    // Build hero separately (OPTION A)
    $hasHero = !empty($meta['hero_title']) || !empty($meta['hero_subtitle']) || !empty($meta['hero_image']);
    if ($hasHero) {
      $hero_title = $meta['hero_title'] ?? ($meta['title'] ?? site('name'));
      $hero_subtitle = $meta['hero_subtitle'] ?? null;
      $hero_image = $meta['hero_image'] ?? null;
      ob_start();
      include path('partials') . '/hero.php';
      $hero_html = ob_get_clean();
    } else {
      $hero_html = '';
    }
  }

  $layout = $meta['layout'] ?? 'main';
  render($layout, compact('title', 'content', 'path', 'meta', 'hero_html'));
  exit;
}

// ---------- Collection routes: /blog or /blog/my-post ----------
if (is_collection($first)) {

  // Collection LIST: /blog
  if (count($parts) === 1) {
    $items = list_collection($first);
    $content = $render_cards($items, ucfirst($first), 'No items yet.', $first);
    $title = ucfirst($first);
    $meta = [];
    render('main', compact('title', 'content', 'path', 'meta'));
    exit;
  }

  // Collection ITEM: /blog/my-post
  $slug = $parts[1] ?? '';
  $item = $slug !== '' ? load_collection_item($first, $slug) : null;

  if (!$item) {
    http_response_code(404);
    $title = 'Not Found';
    $content = '<p>Missing item.</p>';
    $meta = [];
  } else {
    $meta = $item['meta'] ?? [];
    $title = $meta['title'] ?? $slug;
    $content = $item['html'];

    // Append tag list if tags exist
    $tags = $meta['tags'] ?? [];
    if ($tags) {
      $links = array_map(function ($t) {
        return '<a href="' . url('/tags/' . $t) . '">' . htmlspecialchars($t) . '</a>';
      }, $tags);
      $content .= '<p><small>Tags: ' . implode(', ', $links) . '</small></p>';
    }
  }

  $layout = $meta['layout'] ?? 'main';
  render($layout, compact('title', 'content', 'path', 'meta', 'hero_html'));
  exit;
}

// ---------- Tags index: /tags ----------
if ($first === 'tags' && count($parts) === 1) {
  $tagmap = all_tags(); // across all collections

  ob_start();
  echo '<h1>Tags</h1>';
  if (!$tagmap) {
    echo '<p>No tags yet.</p>';
  } else {
    echo '<ul class="tags-list">';
    foreach ($tagmap as $tag => $count) {
      $href = url('/tags/' . rawurlencode($tag));
      $label = htmlspecialchars($tag);
      echo "<li><a class=\"tag-chip\" href=\"$href\">$label <span class=\"tag-count\">$count</span></a></li>";
    }
    echo '</ul>';
  }
  $content = ob_get_clean();

  $title = 'Tags';
  $meta = [];
  render('main', compact('title', 'content', 'path', 'meta'));
  exit;
}

// ---------- Single tag: /tags/{tag} ----------
if ($first === 'tags' && !empty($parts[1])) {
  $tag = urldecode($parts[1]);
  $items = items_with_tag($tag); // across all collections
  $content = $render_cards($items, 'Tag: ' . $tag, 'No items with this tag yet.');
  $title = 'Tag: ' . $tag;
  $meta = [];
  render('main', compact('title', 'content', 'path', 'meta'));
  exit;
}

// ---------- Per-collection tag: /{collection}/tag/{tag} ----------
if (is_collection($first) && ($parts[1] ?? '') === 'tag' && !empty($parts[2])) {
  $collection = $first;
  $tag = urldecode($parts[2]);
  $items = items_with_tag($tag, $collection);

  $heading = ucfirst($collection) . ' — Tag: ' . $tag;
  $content = $render_cards($items, $heading, 'No items with this tag in this collection.', $collection);
  $title = $heading;
  $meta = [];
  render('main', compact('title', 'content', 'path', 'meta'));
  exit;
}

// ---------- Page route: support nested pages, e.g. /guides/install ----------
$rel = implode('/', $parts);
$page = load_page_path($rel);

if (!$page) {
  http_response_code(404);
  $title = 'Not Found';
  $content = '<p>Page not found.</p>';
  $meta = [];
  $hero_html = '';
} else {
  $meta = $page['meta'] ?? [];
  $title = $meta['title'] ?? ucfirst(basename($rel));
  $content = $page['html']; // just the markdown HTML

  $hasHero = !empty($meta['hero_title']) || !empty($meta['hero_subtitle']) || !empty($meta['hero_image']);
  if ($hasHero) {
    $hero_title = $meta['hero_title'] ?? ($meta['title'] ?? ucfirst(basename($rel)));
    $hero_subtitle = $meta['hero_subtitle'] ?? null;
    $hero_image = $meta['hero_image'] ?? null;
    ob_start();
    include path('partials') . '/hero.php';
    $hero_html = ob_get_clean();
  } else {
    $hero_html = '';
  }
}

$layout = $meta['layout'] ?? 'main';
render($layout, compact('title', 'content', 'path', 'meta', 'hero_html'));
exit;