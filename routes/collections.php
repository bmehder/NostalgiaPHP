<?php
// app/routes/collections.php
// Uses: $parts, $first (provided by index.php)

$collection = $first;

// LIST: /blog
if (count($parts) === 1) {
  $items = list_collection($collection);

  ob_start();
  echo '<h1>' . htmlspecialchars(ucfirst($collection)) . '</h1>';

  if (!$items) {
    echo '<p>No items yet.</p>';
  } else {
    echo '<div class="cards auto-fill">';
    foreach ($items as $it) {
      // variables expected by the card partial
      $item = $it;
      include path('partials') . '/card.php';
    }
    echo '</div>';
  }

  $content = ob_get_clean();
  $title = ucfirst($collection);
  $meta = [];

  // $layout = !empty($meta['layout']) ? $meta['layout'] : 'main';
  render('blog', compact('title', 'content', 'path', 'meta'));
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

$layout = !empty($meta['layout']) ? $meta['layout'] : 'main';
render($layout, compact('title', 'content', 'path', 'meta'));