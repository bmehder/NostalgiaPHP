<?php
// routes/collections.php
// Uses: $parts, $first (provided by index.php)

$collection = $first;

// LIST: /blog
if (count($parts) === 1) {
  // Fetch all items in this collection
  $all = list_collection($collection) ?? [];

  // Read per_page from config, default to 9
  $cfg = config();
  $perPage = $cfg['collections'][$collection]['per_page'] ?? 9;

  $total = count($all);
  $pages = max(1, (int) ceil($total / $perPage));
  $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
  if ($page > $pages)
    $page = $pages;

  $offset = ($page - 1) * $perPage;
  $itemsPage = array_slice($all, $offset, $perPage);

  // Helper: build links like /blog?page=2
  $buildPageHref = function (int $p) use ($collection) {
    $qs = $_GET;
    if ($p === 1) {
      unset($qs['page']); // don’t include ?page=1
    } else {
      $qs['page'] = $p;
    }
    $qstr = http_build_query($qs);
    return url('/' . $collection . ($qstr ? ('?' . $qstr) : ''));
  };

  ob_start();
  echo '<h1>' . htmlspecialchars(ucfirst($collection), ENT_QUOTES, 'UTF-8') . '</h1>';
  echo '<h2 class="visually-hidden">' . htmlspecialchars(ucfirst($collection), ENT_QUOTES, 'UTF-8') . ' posts</h2>';

  if (!$itemsPage) {
    echo '<p>No items yet.</p>';
  } else {
    $items = $itemsPage;
    include path('partials') . '/cards-grid.php';

    if ($pages > 1) {
      echo '<nav class="pager" style="display:flex;gap:.5rem;align-items:center;justify-content:center;margin-block-start:var(--size-2)">';
      if ($page > 1) {
        echo '<a class="button" href="' . htmlspecialchars($buildPageHref($page - 1), ENT_QUOTES, 'UTF-8') . '">← Prev</a>';
      } else {
        echo '<span class="button" aria-disabled="true" style="opacity:.5;pointer-events:none">← Prev</span>';
      }

      echo '<span class="muted" style="padding:.25rem .5rem">Page ' . $page . ' of ' . $pages . '</span>';

      if ($page < $pages) {
        echo '<a class="button" href="' . htmlspecialchars($buildPageHref($page + 1), ENT_QUOTES, 'UTF-8') . '">Next →</a>';
      } else {
        echo '<span class="button" aria-disabled="true" style="opacity:.5;pointer-events:none">Next →</span>';
      }
      echo '</nav>';
    }
  }

  $content = ob_get_clean();

  // pull per-collection config
  $cfg = config();
  $colCfg = $cfg['collections'][$collection] ?? [];

  // description with fallback
  $desc = $colCfg['description'] ?? (ucfirst($collection) . ' entries');
  if ($page > 1)
    $desc .= " — Page $page of $pages";

  // title + meta
  $title = ucfirst($collection);
  $meta = ['description' => $desc];

  // template stays “collection” unless overridden later
  $template = 'collection';

  render($template, compact('title', 'content', 'path', 'meta'));
  return;
}

// ITEM: /blog/my-post
$slug = $parts[1] ?? '';
$item = $slug !== '' ? load_collection_item($collection, $slug) : null;

if (!$item) {
  http_response_code(404);
  $title = 'Not Found';
  $meta = [];
  $content = '<p>Missing item.</p>';
} else {
  $title = $item['meta']['title'] ?? $slug;
  $meta = $item['meta'] ?? [];
  $content = $item['html'];
}

$template = !empty($meta['template']) ? $meta['template'] : 'main';
render($template, compact('title', 'content', 'path', 'meta'));