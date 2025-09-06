<?php
// Minimal helpers for routing, loading, front-matter, and markdown

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
  return array_key_exists($segment, config()['collections']);
}

function read_file($file)
{
  return is_file($file) ? file_get_contents($file) : null;
}

function parse_front_matter($raw)
{
  $meta = [];
  $body = $raw;
  if (preg_match('/^---\\s*\\n(.+?)\\n---\\s*\\n(.*)$/s', $raw, $m)) {
    $yaml = trim($m[1]);
    $body = $m[2];
    foreach (preg_split('/\\r?\\n/', $yaml) as $line) {
      if (!trim($line))
        continue;
      if (preg_match('/^([A-Za-z0-9_\\-]+):\\s*(.*)$/', $line, $p)) {
        $k = trim($p[1]);
        $v = trim($p[2]);
        // Basic typing for dates & booleans
        if (preg_match('/^\\d{4}-\\d{2}-\\d{2}/', $v))
          $v = new DateTime($v);
        elseif ($v === 'true')
          $v = true;
        elseif ($v === 'false')
          $v = false;
        $meta[$k] = $v;
        // Normalize tags: "tag1, tag2" -> ['tag1','tag2']
        if ($k === 'tags') {
          // support "a, b, c" or "[a, b, c]" (we'll just strip brackets if present)
          $v = trim($v, "[] \t");
          $parts = array_filter(array_map('trim', explode(',', $v)));
          $meta[$k] = array_values($parts);
        }
      }
    }
  }
  return [$meta, $body];
}

// function markdown_to_html($md)
// {
//   // Tiny Markdown subset: headings, bold/italic, code, links, lists, paragraphs
//   $html = $md;

//   // Headings
//   $html = preg_replace('/^######\\s*(.+)$/m', '<h6>$1</h6>', $html);
//   $html = preg_replace('/^#####\\s*(.+)$/m', '<h5>$1</h5>', $html);
//   $html = preg_replace('/^####\\s*(.+)$/m', '<h4>$1</h4>', $html);
//   $html = preg_replace('/^###\\s*(.+)$/m', '<h3>$1</h3>', $html);
//   $html = preg_replace('/^##\\s*(.+)$/m', '<h2>$1</h2>', $html);
//   $html = preg_replace('/^#\\s*(.+)$/m', '<h1>$1</h1>', $html);

//   // Inline
//   $html = preg_replace('/\\*\\*(.+?)\\*\\*/s', '<strong>$1</strong>', $html);
//   $html = preg_replace('/\\*(.+?)\\*/s', '<em>$1</em>', $html);
//   $html = preg_replace('/`([^`]+)`/', '<code>$1</code>', $html);
//   $html = preg_replace('/\$begin:math:display$(.+?)\\$end:math:display$\$begin:math:text$(https?:[^\\$end:math:text$]+)\\)/', '<a href="$2">$1</a>', $html);

//   // Lists (very naive)
//   $html = preg_replace_callback('/(^|\\n)(?:-\\s.+\\n?)+/m', function ($m) {
//     $items = preg_replace('/^-\\s(.+)$/m', '<li>$1</li>', trim($m[0]));
//     return "\n<ul>\n$items\n</ul>\n";
//   }, $html);

//   // Ordered lists (very naive)
//   $html = preg_replace_callback('/(^|\n)(?:\d+\.\s.+\n?)+/m', function ($m) {
//     $items = preg_replace('/^\d+\.\s(.+)$/m', '<li>$1</li>', trim($m[0]));
//     return "\n<ol>\n$items\n</ol>\n";
//   }, $html);

//   // Paragraphs: wrap plain blocks not already HTML
//   $blocks = preg_split('/\\n\\n+/', trim($html));
//   $blocks = array_map(function ($block) {
//     // AFTER (broader: recognize common HTML block tags)
//     if (preg_match('/^\s*<\/?(article|section|div|header|footer|nav|main|aside|figure|figcaption|h\d|p|ul|ol|li|pre|blockquote|code|table|thead|tbody|tr|td|th|img|video|iframe|form|input|button|label|span|a)\b/i', $block)) {
//       return $block;
//     }
//     return '<p>' . $block . '</p>';
//   }, $blocks);

//   return implode("\n\n", $blocks);
// }


function markdown_to_html($md)
{
  // --- PREPROCESS: ```gallery ... ``` fences on raw Markdown ---
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
        $src = $srcInput;                 // external URL
      } elseif ($srcInput[0] === '/') {
        $src = url($srcInput);            // site-absolute path
      } else {
        $src = url('/assets/' . $srcInput); // relative → /assets/...
      }

      $alt = $caption !== '' ? $caption : preg_replace('/\.[a-z0-9]+$/i', '', basename($srcInput));
      $images[] = ['src' => $src, 'alt' => $alt];
    }

    if (!$images)
      return '';

    ob_start();
    include path('partials') . '/gallery.php';
    return ob_get_clean();
  }, $md);
  
  static $engine = null;

  if ($engine === null) {
    // Manual include of Parsedown.php
    if (is_file(__DIR__ . '/Parsedown.php')) {
      require_once __DIR__ . '/Parsedown.php';
      $engine = new \Parsedown();
    } else {
      $engine = false; // fallback tiny parser if you kept it
    }

    if ($engine) {
      // Trusted content → keep SafeMode off
      if (method_exists($engine, 'setSafeMode')) {
        $engine->setSafeMode(false);
      }
      if (method_exists($engine, 'setBreaksEnabled')) {
        $engine->setBreaksEnabled(false); // keep Markdown’s standard line breaks
      }
    }
  }

  if ($engine) {
    return $engine->text($md);
  }

  // ---- fallback tiny parser if you want to keep it ----
  return $md;
}

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
  // collapse duplicate slashes
  return preg_replace('#/+#', '/', $p);
}

function load_page_path($rel)
{
  $rel = sanitize_rel_path($rel);
  if (!$rel)
    return null;

  $base = path('pages');

  // Try a direct file first, then an index.md under a folder
  $candidates = [
    $base . '/' . $rel . '.md',        // e.g. content/pages/guides/install.md
    $base . '/' . $rel . '/index.md',  // e.g. content/pages/guides/index.md
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
    $items[] = ['slug' => $slug, 'meta' => $meta];
  }
  // Sorting by meta key (e.g., date)
  $cfg = config()['collections'][$collection] ?? null;
  if ($cfg && isset($cfg['sort'])) {
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
  // filter out drafts
  $items = array_filter($items, function ($it) {
    return empty($it['meta']['draft']);
  });
  return $items;
}

function excerpt_from_html($html, $max = 160)
{
  if (!is_string($html))
    return ''; // guard against null/other
  $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
  if (mb_strlen($text) <= $max)
    return $text;
  $cut = mb_substr($text, 0, $max);
  // avoid chopping mid-word
  $cut = preg_replace('/\s+\S*$/u', '', $cut);
  return rtrim($cut, " \t\n\r\0\x0B.,;:!?\u{200B}") . '…';
}

function items_with_tag($tag, $collection = null)
{
  $results = [];
  $collections = $collection ? [$collection] : array_keys(config()['collections']);
  foreach ($collections as $c) {
    foreach (list_collection($c) as $it) {
      $tags = $it['meta']['tags'] ?? [];
      if (in_array($tag, $tags, true)) {
        $it['meta']['collection'] = $c;
        $results[] = $it;
      }
    }
  }
  return $results;
}

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