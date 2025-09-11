<?php
// sitemap.php
// Minimal /sitemap.xml endpoint for NostalgiaPHP.
// Include from index.php after $path/$parts/$first are set.

if (($path ?? '') === 'sitemap.xml') {
  $includeCollectionIndexes = false;

  // keep XML clean even if notices happen
  $old_display = ini_get('display_errors');
  ini_set('display_errors', '0');
  ob_start();

  $xml = function ($s) {
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_XML1, 'UTF-8'); };

  // --- ensure absolute base URL ---
  $baseUrl = rtrim((string) site('base_url'), '/');
  if ($baseUrl === '' || $baseUrl === '/') {
    // Build from current host as a fallback
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = $scheme . '://' . $host;
  }

  // Helper that makes absolute locs
  $abs = function (string $path) use ($baseUrl): string {
    return $baseUrl . '/' . ltrim($path, '/');
  };

  $entries = [];

  // --- Pages ---
  $basePages = rtrim(path('pages'), '/');
  if (is_dir($basePages)) {
    $it = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($basePages, FilesystemIterator::SKIP_DOTS)
    );
    $rels = [];
    foreach ($it as $f) {
      if (!$f->isFile() || strtolower($f->getExtension()) !== 'md')
        continue;
      $absPath = $f->getPathname();
      $rel = trim(str_replace($basePages, '', $absPath), '/');  // "guides/install.md" or "index.md"
      $rel = preg_replace('/\.md$/i', '', $rel);                // drop .md
      if ($rel === 'index')
        $rel = '';
      if (substr($rel, -6) === '/index')
        $rel = substr($rel, 0, -6);
      $rels[] = $rel;
    }
    $rels = array_values(array_unique($rels));
    sort($rels, SORT_NATURAL | SORT_FLAG_CASE);

    foreach ($rels as $rel) {
      $file = ($rel === '')
        ? $basePages . '/index.md'
        : (is_file($basePages . '/' . $rel . '.md') ? $basePages . '/' . $rel . '.md' : $basePages . '/' . $rel . '/index.md');
      if (!is_file($file))
        continue;

      [$meta] = parse_front_matter(read_file($file));
      if (!empty($meta['draft']))
        continue;
      if (isset($meta['sitemap']) && $meta['sitemap'] === false)
        continue;

      $loc = $rel === '' ? $abs('/') : $abs('/' . $rel);
      $lastmod = gmdate('c', filemtime($file) ?: time());
      $entries[] = ['loc' => $loc, 'lastmod' => $lastmod];
    }
  }

  // --- Collections (items only) ---
  $collections = array_keys(config()['collections'] ?? []);
  foreach ($collections as $c) {
    if ($includeCollectionIndexes) {
      $entries[] = ['loc' => $abs('/' . $c), 'lastmod' => null];
    }
    foreach (list_collection($c) as $it) {
      $m = $it['meta'] ?? [];
      if (!empty($m['draft']))
        continue; // extra safety
      if (isset($m['sitemap']) && $m['sitemap'] === false)
        continue;

      $file = path('collections') . "/$c/{$it['slug']}.md";
      $loc = $abs("/$c/{$it['slug']}");
      $lastmod = is_file($file) ? gmdate('c', filemtime($file)) : null;

      $entries[] = ['loc' => $loc, 'lastmod' => $lastmod];
    }
  }

  // --- De-dupe by loc & sort for stable output ---
  $byLoc = [];
  foreach ($entries as $e) {
    $byLoc[$e['loc']] = $e;
  } // later wins, which is fine here
  $entries = array_values($byLoc);
  usort($entries, fn($a, $b) => strcmp($a['loc'], $b['loc']));

  // output XML
  ob_clean();
  header('Content-Type: application/xml; charset=utf-8');
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
  foreach ($entries as $e) {
    echo "  <url>\n";
    echo "    <loc>" . $xml($e['loc']) . "</loc>\n";
    if (!empty($e['lastmod'])) {
      echo "    <lastmod>" . $xml($e['lastmod']) . "</lastmod>\n";
    }
    echo "  </url>\n";
  }
  echo "</urlset>\n";

  ini_set('display_errors', $old_display);
  exit;
}