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
  if (!$item) { http_response_code(404); $title='Not Found'; $content='<p>Missing item.</p>'; }
  else { $title = $item['meta']['title'] ?? $slug; $content = $item['html']; }
  render('main', compact('title','content'));
  exit;
}

// Page route: /about -> content/pages/about.md
$page = load_page($first);
if (!$page) { http_response_code(404); $title='Not Found'; $content='<p>Page not found.</p>'; }
else { $title = $page['meta']['title'] ?? ucfirst($first); $content = $page['html']; }
render('main', compact('title','content'));
