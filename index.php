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
  render('main', compact('title', 'content'));
  exit;
}

$parts = explode('/', $path);
$first = $parts[0];

// Collection routes: /blog  or /blog/my-post
if (is_collection($first)) {
  // Collection LIST: /blog
  if (count($parts) === 1) {
    $items = list_collection($first);
    ob_start();
    echo '<h1>' . htmlspecialchars(ucfirst($first)) . '</h1>';

    if (!$items) {
      echo '<p>No items yet.</p>';
    } else {
      echo '<div class="cards auto-fit">';
      foreach ($items as $it) {
        // Make vars available to the partial
        $collection = $first;
        $item = $it;
        include path('partials') . '/card.php';
      }
      echo '</div>';
    }

    $content = ob_get_clean();
    $title = ucfirst($first);
    render('main', compact('title', 'content'));
    exit;
  }

  // Collection ITEM: /blog/my-post
  $slug = $parts[1];
  $item = load_collection_item($first, $slug);

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
      $links = array_map(
        fn($t) => '<a href="' . url('/tags/' . $t) . '">' . htmlspecialchars($t) . '</a>',
        $tags
      );
      $content .= '<p><small>Tags: ' . implode(', ', $links) . '</small></p>';
    }
  }

  render('main', compact('title', 'content'));
  exit;
}

// Global tag route: /tags/{tag}
if ($first === 'tags' && !empty($parts[1])) {
  $tag = $parts[1];
  $items = items_with_tag($tag); // all collections
  ob_start();
  echo '<h1>Tag: ' . htmlspecialchars($tag) . '</h1>';
  if (!$items)
    echo '<p>No items with this tag yet.</p>';
  else {
    echo '<ul>';
    foreach ($items as $it) {
      $c = htmlspecialchars($it['meta']['collection'] ?? '');
      $title = htmlspecialchars($it['meta']['title'] ?? $it['slug']);
      $href = url("/{$c}/{$it['slug']}");
      echo "<li><a href=\"$href\">$title</a> <small>in $c</small></li>";
    }
    echo '</ul>';
  }
  $content = ob_get_clean();
  $title = 'Tag: ' . $tag;
  render('main', compact('title', 'content'));
  exit;
}

// Per-collection tag route: /{collection}/tag/{tag}
if (is_collection($first) && (isset($parts[1]) && $parts[1] === 'tag') && !empty($parts[2])) {
  $collection = $first;
  $tag = $parts[2];
  $items = items_with_tag($tag, $collection);
  ob_start();
  echo '<h1>' . htmlspecialchars(ucfirst($collection)) . ' — Tag: ' . htmlspecialchars($tag) . '</h1>';
  if (!$items)
    echo '<p>No items with this tag yet.</p>';
  else {
    echo '<ul>';
    foreach ($items as $it) {
      $title = htmlspecialchars($it['meta']['title'] ?? $it['slug']);
      $href = url("/{$collection}/{$it['slug']}");
      echo "<li><a href=\"$href\">$title</a></li>";
    }
    echo '</ul>';
  }
  $content = ob_get_clean();
  $title = ucfirst($collection) . " — $tag";
  render('main', compact('title', 'content'));
  exit;
}

// Page route: support nested pages, e.g. /guides/install
$rel = implode('/', $parts);
$page = load_page_path($rel);

if (!$page) {
  http_response_code(404);
  $title = 'Not Found';
  $content = '<p>Page not found.</p>';
} else {
  $title = $page['meta']['title'] ?? ucfirst(basename($rel));

  // If front-matter defines `hero`, render the hero partial before the content
  if (!empty($page['meta']['hero'])) {
    ob_start();
    $subtitle = (string) $page['meta']['hero']; // from front-matter
    include path('partials') . '/hero.php';
    echo $page['html'];                         // then the normal page content
    $content = ob_get_clean();
  } else {
    $content = $page['html'];
  }
}

render('main', compact('title', 'content'));
