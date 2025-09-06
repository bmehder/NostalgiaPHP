<?php
$cfg = require __DIR__ . '/config.php';
require __DIR__ . '/functions.php';

date_default_timezone_set(site('timezone') ?: 'UTC');

$path = trim(request_path(), '/');

if ($path === '' || $path === '/') {
  $page = load_page('index');
  if (!$page) { http_response_code(404); $title='Not Found'; $content='<p>Create content/pages/index.md</p>'; }
  else { $title = $page['meta']['title'] ?? site('name'); $content = $page['html']; }
  render('main', compact('title','content'));
  exit;
}

$parts = explode('/', $path);
$first = $parts[0];

// Collection routes: /blog  or /blog/my-post
if (is_collection($first)) {
  if (count($parts) === 1) {
    $items = list_collection($first);
    ob_start();
    echo '<h1>' . htmlspecialchars(ucfirst($first)) . '</h1>';
    if (!$items) echo '<p>No items yet.</p>';
    else {
      echo '<ul>';
      foreach ($items as $it) {
        $title = $it['meta']['title'] ?? $it['slug'];
        $date = $it['meta']['date'] instanceof DateTime ? $it['meta']['date']->format('Y-m-d') : '';
        $href = url("/$first/{$it['slug']}");
        echo "<li><a href=\"$href\">" . htmlspecialchars($title) . "</a> <small>$date</small></li>";
      }
      echo '</ul>';
    }
    $content = ob_get_clean();
    $title = ucfirst($first);
    render('main', compact('title','content'));
    exit;
  }
  // Item view
  $slug = $parts[1];
  $item = load_collection_item($first, $slug);
  if (!$item) { http_response_code(404); $title='Not Found'; $content='<p>Missing item.</p>'; } else {
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
  render('main', compact('title','content'));
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

// Page route: /about -> content/pages/about.md
$page = load_page($first);
if (!$page) { http_response_code(404); $title='Not Found'; $content='<p>Page not found.</p>'; }
else { $title = $page['meta']['title'] ?? ucfirst($first); $content = $page['html']; }
render('main', compact('title','content'));
