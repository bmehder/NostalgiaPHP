<?php
// Minimal helpers for routing, loading, front-matter, and markdown

/* ------------------------------ Config & paths ----------------------------- */

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
      'partials' => __DIR__ . '/templates/partials',
      'data' => __DIR__ . '/static/data',
      'static' => '/static',
    ],
    'api' => [
      'enabled' => true,
      'cors_allowlist' => [], // e.g. ['https://example.com']
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

/* ------------------------------ Content Helpers ----------------------------- */

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
function read_file($file): ?string
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
 * Convert Markdown to HTML using Parsedown if available.
 *
 * @param string $md
 * @return string
 */
function markdown_to_html(string $md): string
{
  static $engine = null;

  if ($engine === null) {
    if (is_file(__DIR__ . '/Parsedown.php')) {
      require_once __DIR__ . '/Parsedown.php';
      $engine = new \Parsedown();
      if (method_exists($engine, 'setSafeMode'))
        $engine->setSafeMode(false);
      if (method_exists($engine, 'setBreaksEnabled'))
        $engine->setBreaksEnabled(false);
      if (method_exists($engine, 'setMarkupEscaped'))
        $engine->setMarkupEscaped(false);
    } else {
      $engine = false;
    }
  }

  // Always return a string
  return $engine ? (string) $engine->text($md) : (string) $md;
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
  $meta['_collection'] = $collection; // expose collection to templates/helpers
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
// function all_items($only_collection = null)
// {
//   $items = [];
//   $cfg = config();
//   $collections = $only_collection ? [$only_collection] : array_keys($cfg['collections'] ?? []);

//   foreach ($collections as $c) {
//     foreach (list_collection($c) as $it) {
//       // remember the collection
//       $it['meta']['_collection'] = $c;
//       $items[] = $it;
//     }
//   }
//   return $items;
// }

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

/**
 * Extract a DateTime from front matter if present.
 *
 * @param array $meta
 * @return ?DateTime
 */
function meta_date(?array $meta): ?DateTime
{
  $meta = $meta ?? [];
  if (!array_key_exists('date', $meta) || $meta['date'] === null || $meta['date'] === '') {
    return null;
  }
  $d = $meta['date'];
  if ($d instanceof DateTime) {
    return $d;
  }
  if (is_string($d)) {
    try {
      return new DateTime($d);
    } catch (Throwable $e) {
      return null;
    }
  }
  return null;
}

/**
 * Format the meta date as a string (escaped). Returns '' if no date.
 *
 * @param array  $meta
 * @param string $format
 * @return string
 */
function format_meta_date(?array $meta, string $format = 'Y-m-d'): string
{
  $dt = meta_date($meta);
  if (!$dt) return '';
  return htmlspecialchars($dt->format($format), ENT_QUOTES, 'UTF-8');
}

/**
 * Return a <time> tag for the meta date or '' if none.
 *
 * @param array  $meta
 * @param string $format Human-facing format (default 'M j, Y')
 * @return string
 */
function meta_date_tag(?array $meta, string $format = 'M j, Y'): string
{
  $dt = meta_date($meta);
  if (!$dt) return '';
  $iso = htmlspecialchars($dt->format(DateTime::ATOM), ENT_QUOTES, 'UTF-8');
  $txt = htmlspecialchars($dt->format($format), ENT_QUOTES, 'UTF-8');
  return '<time datetime="' . $iso . '">' . $txt . '</time>';
}

/**
 * True if this meta belongs to a collection item.
 *
 * @param array $meta
 */
function is_collection_item_meta(?array $meta): bool
{
  return is_array($meta) && array_key_exists('_collection', $meta);
}

/* -------------------------- Active Page Helper ------------------------------- */

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

/**
 * Check if a navigation link should be marked "active."
 *
 * @param string $href         The target href of the nav link (e.g. "/about").
 * @param string $current_path The current request path (e.g. "/about/team").
 * @param bool   $prefix       If true, treat the href as a prefix (so "/about"
 *                             is active for "/about" and "/about/...").
 * @return bool True if the link should be considered active, otherwise false.
 */
function is_active(string $href, string $current_path, bool $prefix = false): bool
{
  $href_norm = rtrim($href, '/') ?: '/';
  $path_norm = '/' . trim($current_path, '/');
  if ($path_norm === '//')
    $path_norm = '/';

  if (!$prefix) {
    return $href_norm === $path_norm;
  }

  // prefix mode: /about is active for /about and /about/*
  if ($href_norm === '/') {
    return $path_norm === '/'; // don't make everything active on root
  }
  return $path_norm === $href_norm || str_starts_with($path_norm, $href_norm . '/');
}

/**
 * Return a CSS class if a link should be marked "active."
 *
 * Wraps `is_active()` to simplify template code:
 * Instead of writing a full if/else, you just echo this function in `class=""`.
 *
 * @param string $href         The target href of the nav link (e.g. "/about").
 * @param string $current_path The current request path (e.g. "/about/team").
 * @param bool   $prefix       If true, treat the href as a prefix (so "/about"
 *                             is active for "/about" and "/about/...").
 * @param string $class        The CSS class name to return when active (default: "active").
 * @return string The class name if active, otherwise an empty string.
 */
function active_class(string $href, string $current_path, bool $prefix = false, string $class = 'active'): string
{
  return is_active($href, $current_path, $prefix) ? $class : '';
}

/* -------------------------------- Rendering ------------------------------- */

function render_tags(array $meta): string
{
  $tagsRaw = $meta['tags'] ?? ($meta['tag'] ?? ($meta['keywords'] ?? []));
  $tags = is_array($tagsRaw)
    ? array_values(array_filter(array_map('trim', $tagsRaw)))
    : array_values(array_filter(array_map('trim', preg_split('/\s*,\s*/', (string) $tagsRaw))));

  if (!$tags)
    return '';

  ob_start();
  $tagsPartial = path('partials') . '/tags.php';
  if (is_file($tagsPartial)) {
    include $tagsPartial; // partial expects $tags
  } else {
    echo '<ul class="tags">';
    foreach ($tags as $tag) {
      $safe = htmlspecialchars((string) $tag, ENT_QUOTES, 'UTF-8');
      echo '<li><a href="' . url('/tag/' . rawurlencode($tag)) . '">' . $safe . '</a></li>';
    }
    echo '</ul>';
  }
  return ob_get_clean();
}

function render_for_build(string $template, string $title, string $content, array $meta, string $urlPath): string
{
  return render_to_string(function () use ($template, $title, $content, $meta, $urlPath) {
    // Ensure templates/partials receive the correct current path
    $path = $urlPath;
    render($template, compact('title', 'content', 'path', 'meta'));
  });
}

/**
 * Build the hero HTML from front-matter.
 * Returns an empty string if no hero fields are present.
 */
function build_hero_html(?array $meta): string
{
  $meta = $meta ?? [];
  $hasHero = !empty($meta['hero_title']) || !empty($meta['hero']) || !empty($meta['hero_image']);
  if (!$hasHero)
    return '';

  $hero_title = $meta['hero_title'] ?? ($meta['title'] ?? '');
  $hero_subtitle = $meta['hero_subtitle'] ?? ($meta['hero'] ?? '');
  $hero_image = $meta['hero_image'] ?? null;
  $hero_button_text = $meta['hero_button_text'] ?? null;
  $hero_button_link = $meta['hero_button_link'] ?? null;

  ob_start();
  include path('partials') . '/hero.php'; // expects $hero_title, $hero_subtitle, $hero_image
  return (string) ob_get_clean();
}

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