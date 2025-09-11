<?php
// Minimal helpers for routing, loading, front-matter, and markdown

/* ------------------------------ Config & paths ----------------------------- */

function config()
{
  static $cfg;
  if (!$cfg)
    $cfg = require __DIR__ . '/config.php';
  return $cfg;
}

function site($key = null)
{
  $site = config()['site'];
  return $key ? ($site[$key] ?? null) : $site;
}

function path($key)
{
  return config()['paths'][$key] ?? null;
}

function url($path = '')
{
  $base = rtrim(site('base_url'), '/');
  return $base . '/' . ltrim($path, '/');
}

function request_path()
{
  $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
  return '/' . ltrim($uri, '/');
}

function is_collection($segment)
{
  $collections = config()['collections'] ?? [];
  return array_key_exists($segment, $collections);
}

function read_file($file)
{
  return is_file($file) ? file_get_contents($file) : null;
}

/* ------------------------------ Front matter ------------------------------ */

function parse_front_matter($raw)
{
  $meta = [];
  $body = $raw;

  // Match optional front matter at the very start
  if (preg_match('/^---\s*\R([\s\S]*?)\R---\s*(?:\R)?([\s\S]*)\z/u', $raw, $m)) {
    $yaml = trim($m[1]);
    $body = $m[2]; // may be empty

    foreach (preg_split('/\R/', $yaml) as $line) {
      if (!trim($line))
        continue;

      if (preg_match('/^([A-Za-z0-9_\-]+):\s*(.*)$/', $line, $p)) {
        $k = trim($p[1]);
        $v = trim($p[2]);

        // Basic typing
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $v)) {
          $v = new DateTime($v);
        } elseif (strcasecmp($v, 'true') === 0) {
          $v = true;
        } elseif (strcasecmp($v, 'false') === 0) {
          $v = false;
        }

        $meta[$k] = $v;
      }
    }
  }

  return [$meta, $body];
}

/* -------------------------------- Markdown -------------------------------- */

function markdown_to_html($md)
{
  // Use Parsedown if available; otherwise return raw (you can use a tiny fallback if you want)
  static $engine = null;

  if ($engine === null) {
    if (is_file(__DIR__ . '/Parsedown.php')) {
      require_once __DIR__ . '/Parsedown.php';
      $engine = new \Parsedown();
      if (method_exists($engine, 'setSafeMode'))
        $engine->setSafeMode(false);
      if (method_exists($engine, 'setBreaksEnabled'))
        $engine->setBreaksEnabled(false);
    } else {
      $engine = false;
    }
  }

  $out = $engine ? $engine->text($md) : $md;

  return $out; // (If you want "Parsedown-or-bust", you can error instead of returning $md)
}

/* --------------------------------- Pages ---------------------------------- */

function load_page($slug)
{
  $file = path('pages') . '/' . $slug . '.md';
  if (!is_file($file))
    return null;
  [$meta, $md] = parse_front_matter(read_file($file));
  $html = markdown_to_html($md);
  return ['type' => 'page', 'slug' => $slug, 'meta' => $meta, 'html' => $html];
}

function sanitize_rel_path($p)
{
  $p = trim($p, '/');
  if ($p === '' || strpos($p, '..') !== false)
    return null; // safety
  return preg_replace('#/+#', '/', $p); // collapse duplicate slashes
}

function load_page_path($rel)
{
  $rel = sanitize_rel_path($rel);
  if (!$rel)
    return null;

  $base = path('pages');
  $candidates = [
    $base . '/' . $rel . '.md',       // e.g. content/pages/guides/install.md
    $base . '/' . $rel . '/index.md', // e.g. content/pages/guides/index.md
  ];

  foreach ($candidates as $file) {
    if (is_file($file)) {
      [$meta, $md] = parse_front_matter(read_file($file));
      $html = markdown_to_html($md);
      return [
        'type' => 'page',
        'path' => $rel,
        'slug' => basename($rel),
        'meta' => $meta,
        'html' => $html,
      ];
    }
  }
  return null;
}

/* ------------------------------- Collections ------------------------------ */

function load_collection_item($collection, $slug)
{
  $file = path('collections') . "/$collection/$slug.md";
  if (!is_file($file))
    return null;
  [$meta, $md] = parse_front_matter(read_file($file));
  $meta['slug'] = $slug;
  $html = markdown_to_html($md);
  return ['type' => 'item', 'collection' => $collection, 'slug' => $slug, 'meta' => $meta, 'html' => $html];
}

function list_collection($collection)
{
  $dir = path('collections') . "/$collection";
  if (!is_dir($dir))
    return [];

  $items = [];
  foreach (glob($dir . '/*.md') as $file) {
    $slug = basename($file, '.md');
    [$meta, $md] = parse_front_matter(read_file($file));
    $meta['slug'] = $slug;

    // Generate HTML so cards can build excerpts reliably
    $html = markdown_to_html($md);

    $items[] = [
      'slug' => $slug,
      'meta' => $meta,
      'html' => $html,
    ];
  }

  // Filter out drafts
  $items = array_filter($items, function ($it) {
    return empty($it['meta']['draft']);
  });

  // Sort by configured key (e.g. ['date','desc'])
  $cfg = config()['collections'][$collection] ?? null;
  if ($cfg && isset($cfg['sort']) && is_array($cfg['sort']) && count($cfg['sort']) === 2) {
    [$key, $dir] = $cfg['sort'];
    usort($items, function ($a, $b) use ($key, $dir) {
      $av = $a['meta'][$key] ?? null;
      $bv = $b['meta'][$key] ?? null;
      if ($av instanceof DateTime)
        $av = $av->getTimestamp();
      if ($bv instanceof DateTime)
        $bv = $bv->getTimestamp();
      if ($av == $bv)
        return 0;
      $cmp = ($av < $bv) ? -1 : 1;
      return ($dir === 'desc') ? -$cmp : $cmp;
    });
  }

  return array_values($items);
}

function all_items($only_collection = null)
{
  $items = [];
  $cfg = config();
  $collections = $only_collection ? [$only_collection] : array_keys($cfg['collections'] ?? []);

  foreach ($collections as $c) {
    foreach (list_collection($c) as $it) {
      // remember the collection
      $it['meta']['_collection'] = $c;
      $items[] = $it;
    }
  }
  return $items;
}

/* ------------------------------- Presentation ----------------------------- */

function excerpt_from_html($html, $max = 160)
{
  if (!is_string($html))
    return '';
  $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
  if (mb_strlen($text) <= $max)
    return $text;
  $cut = mb_substr($text, 0, $max);
  $cut = preg_replace('/\s+\S*$/u', '', $cut); // avoid chopping a word
  return rtrim($cut, " \t\n\r\0\x0B.,;:!?\u{200B}") . '…';
}

/* -------------------------------- Active Page Helper ------------------------------- */

function nav_link($href, $label, $path = '')
{
  $url = url($href);
  $rel = trim($href, '/');
  $path = $path ?? ''; // guard against null
  $active = ($path === $rel || strpos($path, $rel . '/') === 0) ? 'active' : '';
  return '<a href="' . htmlspecialchars($url) . '" class="' . $active . '">' . htmlspecialchars($label) . '</a>';
}

/* -------------------------------- Rendering ------------------------------- */

function render($view, $vars = [])
{
  $vars += [
    'title' => '',
    'content' => '',
    'path' => '',
    'meta' => [],
    'hero_html' => '',
  ];
  $tpl = path('templates') . "/$view.php";
  if (!is_file($tpl)) {
    http_response_code(500);
    echo "Missing template: $view";
    exit;
  }
  extract($vars);
  include $tpl;
}