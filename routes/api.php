<?php
// routes/api.php — tiny JSON API w/ tags
require_once __DIR__ . '/../functions.php';

$cfg = config();
$api = $cfg['api'] ?? [];
if (empty($api['enabled'])) {
  http_response_code(404);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['ok' => false, 'error' => 'API disabled']);
  exit;
}

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowlist = $api['cors_allowlist'] ?? [];

if ($origin && in_array($origin, $allowlist, true)) {
  header("Access-Control-Allow-Origin: $origin");
  header('Vary: Origin');
  header('Access-Control-Allow-Credentials: false');
  header('Access-Control-Allow-Methods: GET, OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type');
}
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
  http_response_code(204);
  exit;
}

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
if ($method !== 'GET') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
  exit;
}

// ---- helpers ----
$send = function ($data, int $status = 200) {
  http_response_code($status);
  echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  exit;
};

/** Extract tags from front matter (tags|tag|keywords, CSV or array). De-dupe case-insensitively; preserve first casing. */
$extractTags = function (array $fm): array {
  $collected = [];
  foreach ($fm as $k => $v) {
    $lk = strtolower((string) $k);
    if ($lk !== 'tags' && $lk !== 'tag' && $lk !== 'keywords')
      continue;

    if (is_string($v)) {
      foreach (array_map('trim', explode(',', $v)) as $t) {
        if ($t !== '')
          $collected[] = $t;
      }
    } elseif (is_array($v)) {
      foreach ($v as $t) {
        if (is_string($t)) {
          $t = trim($t);
          if ($t !== '')
            $collected[] = $t;
        }
      }
    }
  }
  $seen = [];
  $out = [];
  foreach ($collected as $t) {
    $key = mb_strtolower($t);
    if (!isset($seen[$key])) {
      $seen[$key] = true;
      $out[] = $t;
    }
  }
  return $out; // e.g. ['PHP','retro']
};

/** Normalize for comparisons. */
$norm = fn(string $t) => mb_strtolower(trim($t));

/** Front-matter date → 'Y-m-d' or null */
$fmtDate = function ($d): ?string {
  if ($d instanceof DateTime)
    return $d->format('Y-m-d');
  if (is_string($d) && $d !== '')
    return $d;
  return null;
};

// ---- route parsing ----
$path = request_path();                 // e.g. /api/items, /api/pages/about/blink
$parts = explode('/', trim($path, '/')); // ['api','items',...]
array_shift($parts);                     // drop 'api'
$first = strtolower($parts[0] ?? '');

// ---- /api/health ----
if ($first === 'health') {
  $send(['ok' => true, 'app' => 'NostalgiaPHP', 'time' => date('c')]);
}

// Common tag filter params
$getTagFilters = function () use ($norm) {
  $tag = isset($_GET['tag']) ? $norm((string) $_GET['tag']) : null;
  $tags = isset($_GET['tags']) ? array_values(array_filter(array_map($norm, explode(',', (string) $_GET['tags'])))) : [];
  $mode = strtolower($_GET['match'] ?? 'any'); // any|all
  if ($tag && !in_array($tag, $tags, true))
    $tags[] = $tag;
  return [$tags, $mode === 'all' ? 'all' : 'any'];
};
$matchesTagFilter = function (array $itemTags, array $filterTags, string $mode) use ($norm): bool {
  if (!$filterTags)
    return true;
  $lc = array_map($norm, $itemTags);
  if ($mode === 'all') {
    foreach ($filterTags as $t)
      if (!in_array($t, $lc, true))
        return false;
    return true;
  }
  // any
  foreach ($filterTags as $t)
    if (in_array($t, $lc, true))
      return true;
  return false;
};

// ---- /api/items (optional: /api/items/{collection}) ----
if ($first === 'items') {
  $param = $_GET['collection'] ?? null;
  $fromPath = $parts[1] ?? null;
  $collectionFilter = $param ?: $fromPath;

  [$filterTags, $matchMode] = $getTagFilters();

  $root = rtrim(path('collections'), '/');
  if (!is_dir($root))
    $send(['ok' => true, 'count' => 0, 'items' => []]);

  // which collections?
  $collections = [];
  if ($collectionFilter) {
    $dir = $root . '/' . $collectionFilter;
    if (is_dir($dir))
      $collections = [$collectionFilter];
  } else {
    foreach (glob($root . '/*', GLOB_ONLYDIR) as $d)
      $collections[] = basename($d);
  }

  foreach ($collections as $c) {
    foreach (glob($root . '/' . $c . '/*.md') as $mdFile) {
      $slug = basename($mdFile, '.md');

      [$fm, $md] = parse_front_matter(read_file($mdFile) ?? '');
      $html = markdown_to_html($md);

      $title = $fm['title'] ?? ucwords(str_replace(['-', '_'], ' ', $slug));
      $date = $fm['date'] ?? null;
      $dateS = $date instanceof DateTime ? $date->format('Y-m-d') : (is_string($date) ? $date : null);

      $rows[] = [
        'collection' => $c,
        'slug' => $slug,
        'url' => url("/{$c}/{$slug}"),
        'title' => $title,
        'date' => $dateS,
        'image' => $fm['image'] ?? null,  // ← add the image field
        'tags' => $fm['tags'] ?? [],     // ← and tags while we’re here
        'html' => $html,
      ];
    }
  }

  usort($rows, function ($a, $b) {
    $ta = $a['date'] ? @strtotime((string) $a['date']) : 0;
    $tb = $b['date'] ? @strtotime((string) $b['date']) : 0;
    return $tb <=> $ta;
  });

  $send(['ok' => true, 'count' => count($rows), 'items' => $rows]);
}

// ---- /api/pages  (list or /api/pages/{nested/slug}) ----
if ($first === 'pages') {
  [$filterTags, $matchMode] = $getTagFilters();

  $root = rtrim(path('pages'), '/');
  if (!is_dir($root))
    $send(['ok' => true, 'count' => 0, 'pages' => []]);

  // nested slug path support
  $slugPath = isset($parts[1])
    ? implode('/', array_slice($parts, 1))
    : ($_GET['slug'] ?? null);

  // helper to compute URL from relative path (index rules)
  $relToUrl = function (string $rel): string {
    if ($rel === 'index')
      return '/';
    if (substr($rel, -6) === '/index')
      return '/' . substr($rel, 0, -6);
    return '/' . $rel;
  };

  // single page lookup
  if ($slugPath) {
    $candidates = [
      $root . '/' . $slugPath . '.md',
      $root . '/' . $slugPath . '/index.md',
    ];
    foreach ($candidates as $md) {
      if (is_file($md)) {
        [$fm, $mdBody] = parse_front_matter(read_file($md) ?? '');
        $tags = $extractTags((array) $fm);
        if (!$matchesTagFilter($tags, $filterTags, $matchMode))
          $send(['ok' => true, 'count' => 0, 'pages' => []]);
        $title = $fm['title'] ?? ucwords(str_replace(['-', '_'], ' ', basename($slugPath)));
        $dateS = $fmtDate($fm['date'] ?? null);
        $send([
          'ok' => true,
          'count' => 1,
          'pages' => [
            [
              'slug' => $slugPath,
              'url' => $relToUrl($slugPath),
              'title' => $title,
              'date' => $dateS,
              'tags' => $tags,
              'html' => markdown_to_html($mdBody),
            ]
          ],
        ]);
      }
    }
    $send(['ok' => true, 'count' => 0, 'pages' => []]);
  }

  // list all pages (recursive)
  $rows = [];
  $it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
  );
  foreach ($it as $f) {
    if (!$f->isFile() || strtolower($f->getExtension()) !== 'md')
      continue;
    $abs = $f->getPathname();
    $rel = trim(str_replace($root, '', $abs), '/'); // e.g. "about.md" or "guides/install.md"
    $rel = preg_replace('/\.md$/i', '', $rel);

    [$fm, $mdBody] = parse_front_matter(read_file($abs) ?? '');
    $tags = $extractTags((array) $fm);
    if (!$matchesTagFilter($tags, $filterTags, $matchMode))
      continue;

    $title = $fm['title'] ?? ucwords(str_replace(['-', '_'], ' ', basename($rel)));
    $dateS = $fmtDate($fm['date'] ?? null);

    $rows[] = [
      'slug' => $rel,
      'url' => $relToUrl($rel),
      'title' => $title,
      'date' => $dateS,
      'tags' => $tags,
      'html' => markdown_to_html($mdBody),
    ];
  }

  usort($rows, function ($a, $b) {
    $ta = $a['date'] ? @strtotime((string) $a['date']) : 0;
    $tb = $b['date'] ? @strtotime((string) $b['date']) : 0;
    return $tb <=> $ta;
  });

  $send(['ok' => true, 'count' => count($rows), 'pages' => $rows]);
}

// ---- /api/tags  (and /api/tags/{slug}) ----
if ($first === 'tags') {
  $wanted = isset($parts[1]) ? mb_strtolower($parts[1]) : null;

  // Collect all tags across pages + collections with counts.
  $tally = []; // lcTag => ['name'=>lcTag, 'count'=>int]
  $add = function (array $tags) use (&$tally, $norm) {
    foreach ($tags as $t) {
      $k = $norm($t);
      if ($k === '')
        continue;
      $tally[$k] = [
        'tag' => $k,
        'count' => ($tally[$k]['count'] ?? 0) + 1,
      ];
    }
  };

  // scan pages
  $pagesDir = rtrim(path('pages'), '/');
  if (is_dir($pagesDir)) {
    $it = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($pagesDir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $f) {
      if (!$f->isFile() || strtolower($f->getExtension()) !== 'md')
        continue;
      [$fm,] = parse_front_matter(read_file($f->getPathname()) ?? '');
      $add($extractTags((array) $fm));
    }
  }
  // scan collections
  $collRoot = rtrim(path('collections'), '/');
  if (is_dir($collRoot)) {
    foreach (glob($collRoot . '/*', GLOB_ONLYDIR) as $dir) {
      foreach (glob($dir . '/*.md') as $mdFile) {
        [$fm,] = parse_front_matter(read_file($mdFile) ?? '');
        $add($extractTags((array) $fm));
      }
    }
  }

  // If no specific tag requested → return list
  if (!$wanted) {
    // sort by count desc then name asc
    $list = array_values($tally);
    usort($list, function ($a, $b) {
      $c = $b['count'] <=> $a['count'];
      return $c !== 0 ? $c : strcmp($a['tag'], $b['tag']);
    });
    $send(['ok' => true, 'count' => count($list), 'tags' => $list]);
  }

  // /api/tags/{slug}: return all matching pages + items
  [$filterTags, $matchMode] = [[$wanted], 'any']; // single tag
  $results = [];

  // pages
  if (is_dir($pagesDir)) {
    $it = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($pagesDir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($it as $f) {
      if (!$f->isFile() || strtolower($f->getExtension()) !== 'md')
        continue;
      $abs = $f->getPathname();
      $rel = trim(str_replace($pagesDir, '', $abs), '/');
      $rel = preg_replace('/\.md$/i', '', $rel);

      [$fm, $mdBody] = parse_front_matter(read_file($abs) ?? '');
      $tags = $extractTags((array) $fm);
      if (!$matchesTagFilter($tags, $filterTags, $matchMode))
        continue;

      // URL mapping
      if ($rel === 'index')
        $url = '/';
      elseif (substr($rel, -6) === '/index')
        $url = '/' . substr($rel, 0, -6);
      else
        $url = '/' . $rel;

      $results[] = [
        'type' => 'page',
        'slug' => $rel,
        'url' => url($url),
        'title' => $fm['title'] ?? ucwords(str_replace(['-', '_'], ' ', basename($rel))),
        'date' => $fmtDate($fm['date'] ?? null),
        'tags' => $tags,
        'html' => markdown_to_html($mdBody),
      ];
    }
  }

  // items
  if (is_dir($collRoot)) {
    foreach (glob($collRoot . '/*', GLOB_ONLYDIR) as $dir) {
      $collection = basename($dir);
      foreach (glob($dir . '/*.md') as $mdFile) {
        $slug = basename($mdFile, '.md');
        [$fm, $md] = parse_front_matter(read_file($mdFile) ?? '');
        $tags = $extractTags((array) $fm);
        if (!$matchesTagFilter($tags, $filterTags, $matchMode))
          continue;

        $results[] = [
          'type' => 'item',
          'collection' => $collection,
          'slug' => $slug,
          'url' => url("/{$collection}/{$slug}"),
          'title' => $fm['title'] ?? ucwords(str_replace(['-', '_'], ' ', $slug)),
          'date' => $fmtDate($fm['date'] ?? null),
          'tags' => $tags,
          'html' => markdown_to_html($md),
        ];
      }
    }
  }

  usort($results, function ($a, $b) {
    $ta = $a['date'] ? @strtotime((string) $a['date']) : 0;
    $tb = $b['date'] ? @strtotime((string) $b['date']) : 0;
    return $tb <=> $ta;
  });

  $send(['ok' => true, 'count' => count($results), 'tag' => $wanted, 'results' => $results]);
}

// GET /api or /api/
if ($first === '' || $first === null) {
  $collections = array_keys(config()['collections'] ?? []);
  $routes = [
    '/api/health' => 'Basic health check',
    '/api/items' => 'List all items across collections',
    '/api/pages' => 'List all pages',
    '/api/pages/{slug}' => 'Fetch a specific page (supports nested slugs)',
    '/api/tags' => 'List all tags',
  ];

  // Add collection-specific routes dynamically
  foreach ($collections as $c) {
    $routes["/api/items/$c"] = "List items in the \"$c\" collection";
  }

  $send([
    'ok' => true,
    'api' => 'NostalgiaPHP',
    'routes' => $routes,
  ]);
}

// ---- fallback ----
$send(['ok' => false, 'error' => 'Not found'], 404);