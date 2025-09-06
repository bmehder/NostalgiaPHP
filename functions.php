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

  // ---<yaml>--- body ---</yaml>--- (simple YAML-ish parser)
  if (preg_match('/^---\s*\n(.+?)\n---\s*\n(.*)$/s', $raw, $m)) {
    $yaml = trim($m[1]);
    $body = $m[2];

    foreach (preg_split('/\r?\n/', $yaml) as $line) {
      if (!trim($line))
        continue;
      if (preg_match('/^([A-Za-z0-9_\-]+):\s*(.*)$/', $line, $p)) {
        $k = trim($p[1]);
        $v = trim($p[2]);

        // Basic typing
        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $v)) {
          $v = new DateTime($v);
        } elseif ($v === 'true') {
          $v = true;
        } elseif ($v === 'false') {
          $v = false;
        }

        // Normalize tags from a comma string: "a, b, c"
        if ($k === 'tags') {
          $v = trim($v, "[] \t");
          $parts = array_filter(array_map('trim', explode(',', $v)));
          $v = array_values($parts);
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
  // PRE-PROCESS: ```gallery ... ``` (on raw Markdown, before parsing)
  $md = preg_replace_callback('/```gallery\s*\n([\s\S]*?)\n```/i', function ($m) {
    $raw = trim($m[1]);
    if ($raw === '')
      return '';

    $lines = array_filter(array_map('trim', preg_split('/\r?\n/', $raw)));
    $images = [];

    foreach ($lines as $line) {
      [$srcInput, $caption] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
      if ($srcInput === '')
        continue;

      if (preg_match('#^https?://#i', $srcInput)) {
        $src = $srcInput;                   // external
      } elseif ($srcInput[0] === '/') {
        $src = url($srcInput);              // site-absolute
      } else {
        $src = url('/assets/' . $srcInput); // relative -> /assets/...
      }

      // Allow explicit blank caption with a trailing pipe
      if ($caption === '' && strpos($line, '|') !== false) {
        $alt = '';
      } elseif ($caption !== '') {
        $alt = $caption;
      } else {
        $alt = preg_replace('/\.[a-z0-9]+$/i', '', basename($srcInput));
      }

      $images[] = ['src' => $src, 'alt' => $alt];
    }

    if (!$images)
      return '';

    ob_start();
    // expects $images
    include path('partials') . '/gallery.php';
    return ob_get_clean();
  }, $md);

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

  if ($engine)
    return $engine->text($md);

  return $md; // last-resort fallback
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

/* ------------------------------ Tags utilities ---------------------------- */

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

function all_tags($only_collection = null)
{
  $map = [];
  foreach (all_items($only_collection) as $it) {
    $tags = (isset($it['meta']['tags']) && is_array($it['meta']['tags'])) ? $it['meta']['tags'] : [];
    foreach ($tags as $t) {
      if ($t === '' || $t === null)
        continue;
      $key = (string) $t;
      $map[$key] = isset($map[$key]) ? $map[$key] + 1 : 1;
    }
  }
  ksort($map, SORT_NATURAL | SORT_FLAG_CASE);
  return $map;
}

function items_with_tag($tag, $only_collection = null)
{
  $out = [];
  $want = (string) $tag;
  foreach (all_items($only_collection) as $it) {
    $tags = (isset($it['meta']['tags']) && is_array($it['meta']['tags'])) ? $it['meta']['tags'] : [];
    if (in_array($want, $tags, true)) {
      $out[] = $it;
    }
  }
  return $out;
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

/* -------------------------------- Rendering ------------------------------- */

function render($view, $vars = [])
{
  $tpl = path('templates') . "/$view.php";
  if (!is_file($tpl)) {
    http_response_code(500);
    echo "Missing template: $view";
    exit;
  }
  extract($vars);
  include $tpl;
}