<?php
// routes/api.php — super-minimal JSON API (POC)
require_once __DIR__ . '/../functions.php';

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'GET') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
  exit;
}

// tiny JSON helper
$send = function ($data, int $status = 200) {
  http_response_code($status);
  echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  exit;
};

// route segments after /api
$path = request_path();   // e.g. /api/items or /api/items/blog
$parts = explode('/', trim($path, '/'));      // ['api', 'items', 'blog'...]
array_shift($parts);      // drop 'api'
$first = strtolower($parts[0] ?? '');

// GET /api/health
if ($first === 'health') {
  $send(['ok' => true, 'app' => 'NostalgiaPHP', 'time' => date('c')]);
}

// GET /api/items (optional ?collection=blog or /api/items/blog)
if ($first === 'items') {
  $param = $_GET['collection'] ?? null;
  $fromPath = $parts[1] ?? null;
  $collectionFilter = $param ?: $fromPath;   // support both styles

  $root = rtrim(path('collections'), '/');
  if (!is_dir($root)) {
    $send(['ok' => true, 'items' => []]);    // nothing to show yet
  }

  // which collections to scan?
  $collections = [];
  if ($collectionFilter) {
    $dir = $root . '/' . $collectionFilter;
    if (is_dir($dir))
      $collections = [$collectionFilter];
  } else {
    foreach (glob($root . '/*', GLOB_ONLYDIR) as $d) {
      $collections[] = basename($d);
    }
  }

  $rows = [];
  foreach ($collections as $c) {
    foreach (glob($root . '/' . $c . '/*.md') as $md) {
      $slug = basename($md, '.md');
      [$fm, $body] = parse_front_matter(read_file($md) ?? '');
      $title = $fm['title'] ?? ucwords(str_replace(['-', '_'], ' ', $slug));
      $date = $fm['date'] ?? null;
      $dateS = $date instanceof DateTime ? $date->format('Y-m-d') : (is_string($date) ? $date : null);

      $rows[] = [
        'collection' => $c,
        'slug' => $slug,
        'url' => url("/{$c}/{$slug}"),
        'title' => $title,
        'date' => $dateS,
      ];
    }
  }

  // newest first by date if present
  usort($rows, function ($a, $b) {
    $ta = $a['date'] ? @strtotime((string) $a['date']) : 0;
    $tb = $b['date'] ? @strtotime((string) $b['date']) : 0;
    return $tb <=> $ta;
  });

  $send(['ok' => true, 'items' => $rows]);
}

// fallback
$send(['ok' => false, 'error' => 'Not found'], 404);