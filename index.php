<?php
$cfg = require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';

date_default_timezone_set(site('timezone') ?: 'UTC');

$path = trim(request_path(), '/');

// ---------- Home route ----------
if ($path === '' || $path === '/') {
  $page = load_page('index'); // content/pages/index.md
  if (!$page) {
    http_response_code(404);
    $title = 'Not Found';
    $content = '<p>Create content/pages/index.md</p>';
  } else {
    $title = $page['meta']['title'] ?? site('name');

    if (!empty($page['meta']['hero'])) {
      ob_start();
      $subtitle = (string) $page['meta']['hero'];
      $image = $page['meta']['hero_image'] ?? null;
      include path('partials') . '/hero.php';
      echo $page['html'];
      $content = ob_get_clean();
    } else {
      $content = $page['html'];
    }
  }
  render('main', compact('title', 'content', 'path'));
  exit;
}

$parts = $path === '' ? [] : explode('/', $path);
$first = $parts[0] ?? '';

// ---------- Collection routes: /blog  or /blog/my-post ----------
if (is_collection($first)) {
  // Collection LIST: /blog
  if (count($parts) === 1) {
    $items = list_collection($first);
    ob_start();
    echo '<h1>' . htmlspecialchars(ucfirst($first)) . '</h1>';

    if (!$items) {
      echo '<p>No items yet.</p>';
    } else {
      echo '<div class="cards auto-fill">';
      foreach ($items as $it) {
        // make vars available to the partial
        $collection = $first;
        $item = $it;
        include path('partials') . '/card.php';
      }
      echo '</div>';
    }

    $content = ob_get_clean();
    $title = ucfirst($first);
    render('main', compact('title', 'content', 'path'));
    exit;
  }

  // Collection ITEM: /blog/my-post
  $slug = $parts[1] ?? '';
  $item = $slug !== '' ? load_collection_item($first, $slug) : null;

  if (!$item) {
    http_response_code(404);
    $title = 'Not Found';
    $content = '<p>Missing item.</p>';
  } else {
    $title = $item['meta']['title'] ?? $slug;
    $content = $item['html'];

    // Append tag list if tags exist
    $tags = $item['meta']['tags'] ?? [];
    if ($tags) {
      $links = array_map(function ($t) {
        return '<a href="' . url('/tags/' . $t) . '">' . htmlspecialchars($t) . '</a>';
      }, $tags);
      $content .= '<p><small>Tags: ' . implode(', ', $links) . '</small></p>';
    }
  }

  render('main', compact('title', 'content', 'path'));
  exit;
}

// ---------- Tags index: /tags ----------
if ($parts === ['tags'] || ($first === 'tags' && count($parts) === 1)) {
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
  render('main', compact('title', 'content', 'path'));
  exit;
}

// ---------- Single tag: /tags/{tag} ----------
if ($first === 'tags' && !empty($parts[1])) {
  $tag = urldecode($parts[1]);
  $items = items_with_tag($tag); // across all collections

  ob_start();
  echo '<h1>Tag: ' . htmlspecialchars($tag) . '</h1>';
  if (!$items) {
    echo '<p>No items with this tag yet.</p>';
  } else {
    echo '<div class="cards auto-fill">';
    foreach ($items as $it) {
      $collection = $it['meta']['_collection'] ?? 'blog';
      $item = $it;
      include path('partials') . '/card.php';
    }
    echo '</div>';
  }
  $content = ob_get_clean();
  $title = 'Tag: ' . $tag;
  render('main', compact('title', 'content', 'path'));
  exit;
}

// ---------- Per-collection tag: /{collection}/tag/{tag} ----------
if (is_collection($first) && isset($parts[1]) && $parts[1] === 'tag' && !empty($parts[2])) {
  $collection = $first;
  $tag = urldecode($parts[2]);
  $items = items_with_tag($tag, $collection);

  ob_start();
  echo '<h1>' . htmlspecialchars(ucfirst($collection)) . ' — Tag: ' . htmlspecialchars($tag) . '</h1>';
  if (!$items) {
    echo '<p>No items with this tag in this collection.</p>';
  } else {
    echo '<div class="cards auto-fill">';
    foreach ($items as $it) {
      $item = $it;
      include path('partials') . '/card.php';
    }
    echo '</div>';
  }
  $content = ob_get_clean();
  $title = ucfirst($collection) . ' — ' . $tag;
  render('main', compact('title', 'content', 'path'));
  exit;
}

// ---------- Page route: support nested pages, e.g. /guides/install ----------
$rel = implode('/', $parts);
$page = load_page_path($rel);

if (!$page) {
  http_response_code(404);
  $title = 'Not Found';
  $content = '<p>Page not found.</p>';
} else {
  $title = $page['meta']['title'] ?? ucfirst(basename($rel));

  // Optional hero via front matter (hero: "...", hero_image: "/path")
  if (!empty($page['meta']['hero'])) {
    ob_start();
    $subtitle = (string) $page['meta']['hero'];
    $image = $page['meta']['hero_image'] ?? null;
    include path('partials') . '/hero.php';
    echo $page['html'];
    $content = ob_get_clean();
  } else {
    $content = $page['html'];
  }
}

render('main', compact('title', 'content', 'path'));