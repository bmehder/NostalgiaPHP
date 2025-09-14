<?php
// routes/search.php
// Server-side search over pages + collections. Renders HTML using your template.

$title = 'Search';
$q = trim($_GET['q'] ?? '');
$results = [];

// Small helpers
$h = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
$contains = function (string $haystack, string $needle): bool {
  return $needle !== '' && mb_stripos($haystack, $needle) !== false;
};
$highlight = function (string $text, string $needle): string {
  if ($needle === '')
    return $text;
  $re = '/' . preg_quote($needle, '/') . '/iu';
  return preg_replace($re, '<mark>$0</mark>', $text);
};

// Gather searchable items (title, description, url, body text)
$items = [];

// --- Pages ---
$pagesDir = rtrim(path('pages'), '/');
if (is_dir($pagesDir)) {
  $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($pagesDir, FilesystemIterator::SKIP_DOTS));
  foreach ($it as $f) {
    if (!$f->isFile() || strtolower($f->getExtension()) !== 'md')
      continue;

    $abs = $f->getPathname();
    $rel = trim(str_replace($pagesDir, '', $abs), '/');   // e.g. "about.md" or "guides/install.md"
    $rel = preg_replace('/\.md$/i', '', $rel);
    // URL rules: index.md -> '/', foo/index.md -> '/foo', else '/{rel}'
    if ($rel === 'index')
      $url = '/';
    elseif (substr($rel, -6) === '/index')
      $url = '/' . substr($rel, 0, -6);
    else
      $url = '/' . $rel;

    [$meta, $md] = parse_front_matter(read_file($abs));
    if (!empty($meta['draft']))
      continue;
    if (isset($meta['sitemap']) && $meta['sitemap'] === false) {
      // Optional: still searchable; change to "continue;" if you want to exclude from search too.
    }

    $html = markdown_to_html($md);
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));

    $items[] = [
      'title' => (string) ($meta['title'] ?? $rel),
      'desc' => (string) ($meta['description'] ?? ''),
      'url' => $url,
      'text' => $text,
      'date' => $meta['date'] ?? null,
      'kind' => 'page',
    ];
  }
}

// --- Collection items ---
$collections = array_keys(config()['collections'] ?? []);
foreach ($collections as $c) {
  foreach (list_collection($c) as $it) {
    $slug = $it['slug'];
    $meta = $it['meta'] ?? [];
    if (!empty($meta['draft']))
      continue;

    $file = path('collections') . "/$c/$slug.md";
    [$m2, $md] = parse_front_matter(read_file($file));
    // prefer latest meta (parse again), but keep fallbacks
    $title = (string) ($m2['title'] ?? $meta['title'] ?? $slug);
    $desc = (string) ($m2['description'] ?? $meta['description'] ?? '');
    $html = markdown_to_html($md);
    $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));

    $items[] = [
      'title' => $title,
      'desc' => $desc,
      'url' => '/' . $c . '/' . $slug,
      'text' => $text,
      'date' => $m2['date'] ?? ($meta['date'] ?? null),
      'kind' => $c, // show which collection it came from
    ];
  }
}

// When there’s a query, filter and rank
if ($q !== '') {
  $needle = mb_strtolower($q);

  // Score: title matches are strongest, then description, then body text
  $scored = [];
  foreach ($items as $row) {
    $score = 0;
    $titleL = mb_strtolower($row['title']);
    $descL = mb_strtolower($row['desc']);
    $textL = mb_strtolower($row['text']);

    if ($contains($titleL, $needle))
      $score += 100;
    if ($contains($descL, $needle))
      $score += 40;
    if ($contains($textL, $needle))
      $score += 10;

    if ($score > 0) {
      // Build an excerpt from body text around the first hit
      $pos = mb_stripos($row['text'], $q);
      $excerpt = '';
      if ($pos !== false) {
        $start = max(0, $pos - 80);
        $snippet = mb_substr($row['text'], $start, 200);
        // tidy snippet
        $snippet = trim($snippet);
        $excerpt = $snippet . (mb_strlen($row['text']) > ($start + 200) ? '…' : '');
      } else {
        // fallback to description or a generated excerpt
        $excerpt = $row['desc'] !== '' ? $row['desc'] : excerpt_from_html($row['text'], 180);
      }

      // Escape & highlight
      $safeTitle = $highlight($h($row['title']), $q);
      $safeExcerpt = $highlight($h($excerpt), $q);

      $scored[] = [
        'url' => $row['url'],
        'title' => $safeTitle,
        'excerpt' => $safeExcerpt,
        'kind' => $row['kind'],
        'score' => $score,
        'date' => $row['date'],
      ];
    }
  }

  // Sort by score desc, then date desc if available
  usort($scored, function ($a, $b) {
    if ($a['score'] === $b['score']) {
      $ad = $a['date'] instanceof DateTime ? $a['date']->getTimestamp() : 0;
      $bd = $b['date'] instanceof DateTime ? $b['date']->getTimestamp() : 0;
      return $bd <=> $ad;
    }
    return $b['score'] <=> $a['score'];
  });

  $results = $scored;
}

// Render: simple form + results
ob_start(); ?>
<div class="flow">
  <h1>Search</h1>
  
  <form method="get" action="<?= $h(url('/search')) ?>" class="search-form" style="margin-block:1rem;">
    <input type="search" name="q" value="<?= $h($q) ?>" placeholder="Search pages and collections..."
      style="padding:.5rem .75rem; width: min(40rem, 100%);" aria-label="Search query">
    <button type="submit" class="btn" style="padding:.5rem .75rem;">Search</button>
  </form>
  
  <?php if ($q === ''): ?>
    <p class="muted">Type a word or phrase and press Enter.</p>
  <?php else: ?>
    <p><small><?= count($results) ?> result<?= count($results) === 1 ? '' : 's' ?> for
        <strong><?= $h($q) ?></strong></small></p>
  
    <?php foreach ($results as $r): ?>
      <li class="list-style-none" style="border:1px solid #eee; border-radius:8px; padding:1rem;">
        <div style="font-size:.85rem; color:#666; margin-bottom:.25rem;">
          <?= $h($r['kind']) ?>
          <?php if ($r['date'] instanceof DateTime): ?>
            · <time datetime="<?= $r['date']->format('c') ?>">
              <?= $r['date']->format('M j, Y') ?>
            </time>
          <?php endif; ?>
        </div>
        <h3 style="margin:.125rem 0;">
          <a href="<?= $h(url($r['url'])) ?>"><?= $r['title'] ?></a>
        </h3>
        <?php if (!empty($r['excerpt'])): ?>
          <p style="margin:.25rem 0 0;"><?= $r['excerpt'] ?></p>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
$meta = ['title' => 'Search'];
render('main', compact('title', 'content', 'path', 'meta'));