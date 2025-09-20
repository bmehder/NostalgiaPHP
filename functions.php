<?php
// Minimal helpers for routing, loading, front-matter, and markdown

/**
 * Normalize a filesystem path to use forward slashes.
 *
 * @param string $path Input path.
 * @return string Normalized path.
 */
function normalize_path(string $path): string
{
  // Always start with a single leading slash
  $path = '/' . ltrim($path, '/');

  $path = $path == '/' ? $path : rtrim($path, '/');

  return $path;
}

/* ------------------------------ Config & paths ----------------------------- */

/**
 * Get a configuration value.
 *
 * @param string $key Configuration key.
 * @param mixed $default Default value if key is not found.
 * @return mixed The configuration value or default.
 */
function config()
{
  static $cfg;
  if ($cfg)
    return $cfg;

  $user = require __DIR__ . '/config.php';

  // Defaults you don’t want users to worry about
  $defaults = [
    'site' => [
      'name' => 'My Site',
      'base_url' => '/',
      'timezone' => 'UTC',
    ],
    'paths' => [
      'content' => __DIR__ . '/content',
      'pages' => __DIR__ . '/content/pages',
      'collections' => __DIR__ . '/content/collections',
      'templates' => __DIR__ . '/templates',
      'partials' => __DIR__ . '/partials',
      // for URL generation only; file assets still live under project /static
      'static' => '/static',
    ],
  ];

  // Merge (user wins)
  $cfg = array_replace_recursive($defaults, is_array($user) ? $user : []);
  return $cfg;
}

/**
 * Get a site-level configuration value.
 *
 * @param string $key Configuration key (e.g. 'name', 'description').
 * @param mixed $default Default value if key is not found.
 * @return mixed The configuration value or default.
 */
function site($key = null)
{
  // Apply defaults even if user omitted parts of 'site'
  $site = (config()['site'] ?? []) + [
    'name' => 'My Site',
    'base_url' => '/',
    'timezone' => 'UTC',
  ];
  return $key ? ($site[$key] ?? null) : $site;
}

/**
 * Resolve a project-relative path.
 *
 * @param string $key Logical key (e.g. 'templates', 'partials', 'content').
 * @param string $extra Optional trailing segment to append.
 * @return string Full filesystem path.
 */
function path($key)
{
  $paths = config()['paths'] ?? [];
  return $paths[$key] ?? null;
}

/**
 * Resolve a project-relative URL.
 *
 * @param string $path Optional relative path (e.g. '/blog/post').
 * @return string Absolute URL for the current site.
 */
function url(string $rel = ''): string
{
  $base = rtrim(config()['site']['base_url'] ?? '', '/');
  $rel = normalize_path($rel);
  return $base . $rel;
}

/**
 * Get the current request path (without query string).
 *
 * @return string Request path, starting with '/'.
 */
function request_path(): string
{
  $uri = $_SERVER['REQUEST_URI'] ?? '';
  $uri = parse_url($uri, PHP_URL_PATH) ?: '';
  return normalize_path($uri);
}

/**
 * Determine if a given path points to a collection.
 *
 * @param string $dir Directory path.
 * @return bool True if it looks like a collection, false otherwise.
 */
function is_collection($segment)
{
  $collections = config()['collections'] ?? [];
  return array_key_exists($segment, $collections);
}

/**
 * Read the contents of a file safely.
 *
 * @param string $file Path to the file.
 * @return string File contents.
 */
function read_file($file)
{
  return is_file($file) ? file_get_contents($file) : null;
}

/* ------------------------------ Front matter ------------------------------ */

/**
 * Parse front matter (YAML-style) from a Markdown file.
 *
 * @param string $text Full file contents.
 * @return array{0: array<string,mixed>, 1: string} [frontmatter, markdownBody]
 */
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
          try {
            $v = new DateTime($v);
          } catch (Throwable $e) { /* leave as string on failure */
          }
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

/**
 * Convert Markdown to HTML.
 *
 * @param string $markdown Raw Markdown text.
 * @return string HTML output.
 */
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

/**
 * Load a page from the content directory.
 *
 * @param string $slug Page slug or relative path.
 * @return array|null  Array with keys: type, slug, meta, html — or null if not found.
 */
function load_page($slug)
{
  $file = path('pages') . '/' . $slug . '.md';
  if (!is_file($file))
    return null;
  [$meta, $md] = parse_front_matter(read_file($file));
  $html = markdown_to_html($md);
  return ['type' => 'page', 'slug' => $slug, 'meta' => $meta, 'html' => $html];
}

/**
 * Sanitize a relative path to prevent directory traversal.
 *
 * @param string $path Relative path input.
 * @return string Safe, sanitized path.
 */
function sanitize_rel_path($p)
{
  $p = trim($p, '/');
  if ($p === '' || strpos($p, '..') !== false)
    return null; // safety
  return preg_replace('#/+#', '/', $p); // collapse duplicate slashes
}

/**
 * Load a page by an absolute file path.
 *
 * @param string $file Absolute path to Markdown file.
 * @return array|null Array with keys: type, path, slug, meta, html — or null if not found.
 */
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

/**
 * Load a single item from a collection.
 *
 * @param string $collection Collection name.
 * @param string $slug Item slug.
 * @return array|null        Array with keys: type, collection, slug, meta, html — or null if not found.
 */
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

/**
 * List all items in a collection.
 *
 * @param string $collection Collection name.
 * @return array<int,array<string,mixed>> Array of item metadata and content.
 */
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

/**
 * Get all items across all collections.
 *
 * @return array<int,array<string,mixed>> Combined list of all items.
 */
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

/**
 * Generate an excerpt from HTML content.
 *
 * @param string $html HTML content.
 * @param int $length Maximum length of the excerpt.
 * @return string Truncated excerpt with ellipsis if needed.
 */
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

/**
 * Build a navigation link <a> element.
 *
 * @param string $href Target URL.
 * @param string $label Link text.
 * @param string $path Current request path for "active" detection.
 * @return string HTML <a> element string.
 */
function nav_link(string $href, string $label, string $current_path): string
{
  // normalize both
  $href_norm = rtrim($href, '/');
  $path_norm = '/' . trim($current_path, '/');

  // special case: home is just '/'
  if ($href_norm === '') {
    $href_norm = '/';
  }

  $is_active = ($href_norm === $path_norm);

  $class = $is_active ? 'active' : '';
  return '<a href="' . url($href) . '" class="' . $class . '">' . htmlspecialchars($label) . '</a>';
}

/* -------------------------------- Rendering ------------------------------- */

/**
 * Render a template with variables.
 *
 * @param string $view Template name (without .php) under templates/.
 * @param array{
 *   title?: string,
 *   content?: string,
 *   path?: string,
 *   meta?: array<string,mixed>,
 *   hero_html?: string
 * } $vars Variables available to the template. Defaults are applied.
 * @return void
 */
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