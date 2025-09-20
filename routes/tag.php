<?php
// routes/tag.php — list items that have a given tag (collections + pages), scanning files directly.
require_once __DIR__ . '/../functions.php';

$path = request_path();
$parts = $path === '/' ? [] : explode('/', ltrim($path, '/'));
$tag = strtolower(urldecode($parts[1] ?? ''));

if ($tag === '') {
  http_response_code(404);
  render('main', [
    'title' => 'Tag not found',
    'content' => '<p>Missing tag.</p>',
    'path' => $path,
  ]);
  return;
}

/** Extract tags from front matter, tolerant to variations (csv or array; tags/tag/keywords). */
$getTags = function (array $fm): array {
  $candidates = [];
  foreach ($fm as $key => $val) {
    $lk = strtolower((string) $key);
    if ($lk === 'tags' || $lk === 'tag' || $lk === 'keywords') {
      $candidates[] = $val;
    }
  }
  if (!$candidates)
    return [];

  $collected = [];
  foreach ($candidates as $raw) {
    if (is_string($raw)) {
      foreach (array_map('trim', explode(',', $raw)) as $p) {
        if ($p !== '')
          $collected[] = $p;
      }
    } elseif (is_array($raw)) {
      foreach ($raw as $p) {
        if (is_string($p)) {
          $p = trim($p);
          if ($p !== '')
            $collected[] = $p;
        }
      }
    }
  }

  // de-dupe case-insensitively, keep first-seen casing
  $seen = [];
  foreach ($collected as $t) {
    $seen[strtolower($t)] = $t;
  }
  return array_values($seen);
};

$slugToTitle = fn($s) => ucwords(str_replace(['-', '_'], ' ', $s));

// Accumulate results as {url,title,date,ts}
$results = [];

/** Helper: read a .md file, parse FM, match tag, push result */
$checkFile = function (string $absFile, string $urlBuilderType, ?string $collection) use (&$results, $getTags, $slugToTitle, $tag) {
  $raw = @file_get_contents($absFile);
  if ($raw === false)
    return;

  [$fm] = parse_front_matter($raw);

  $tags = array_map('strtolower', $getTags($fm));
  if (!in_array($tag, $tags, true))
    return;

  $slug = basename($absFile, '.md');

  // Build URL per context
  if ($urlBuilderType === 'collection' && $collection !== null) {
    $url = url('/' . $collection . '/' . $slug);
  } else { // pages
    $url = url('/' . ($slug === 'index' ? '' : $slug));
  }

  $title = $fm['title'] ?? $slugToTitle($slug);
  $date = $fm['date'] ?? null;
  $ts = $date instanceof DateTime ? $date->getTimestamp()
    : ($date ? @strtotime((string) $date) : 0);

  $results[] = [
    'url' => $url,
    'title' => $title,
    'date' => $date,
    'ts' => $ts,
  ];
};

// 1) Collections: content/collections/*/*.md
$collRoot = path('content') . '/collections';
if (is_dir($collRoot)) {
  foreach (glob($collRoot . '/*', GLOB_ONLYDIR) as $dir) {
    $collection = basename($dir);
    foreach (glob($dir . '/*.md') as $md) {
      $checkFile($md, 'collection', $collection);
    }
  }
}

// 2) Pages: content/pages/*.md
$pagesDir = path('content') . '/pages';
if (is_dir($pagesDir)) {
  foreach (glob($pagesDir . '/*.md') as $md) {
    $checkFile($md, 'page', null);
  }
}

// Sort newest first
usort($results, fn($a, $b) => $b['ts'] <=> $a['ts']);

// Render a simple list
ob_start();
?>
<h2>Tagged: <?= htmlspecialchars($tag, ENT_QUOTES, 'UTF-8') ?></h2>

<?php if (empty($results)): ?>
  <p>No items with this tag yet.</p>
<?php else: ?>
  <ul class="item-list flow-0-5" style="margin-block-start: var(--size-3)">
    <?php foreach ($results as $row): ?>
      <li class="item-list-row">
        <a class="item-list-link" href="<?= htmlspecialchars($row['url'], ENT_QUOTES, 'UTF-8') ?>">
          <?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?>
        </a>
        <?php
        $d = $row['date'] ?? null;
        $dateStr = $d instanceof DateTime ? $d->format('Y-m-d') : (is_string($d) ? $d : '');
        ?>
        <?php if ($dateStr): ?>
          <small class="item-list-date muted"><?= htmlspecialchars($dateStr, ENT_QUOTES, 'UTF-8') ?></small>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php
$content = ob_get_clean();

render('main', [
  'title' => 'Tagged: ' . htmlspecialchars($tag, ENT_QUOTES, 'UTF-8'),
  'content' => $content,
  'path' => $path,
]);