<?php
// routes/tags.php
// Lists all tags (across collections + pages) with simple counts.

require_once __DIR__ . '/../functions.php';

$normalizeTags = function ($raw): array {
  if (is_string($raw)) {
    $arr = array_map('trim', explode(',', $raw));
  } elseif (is_array($raw)) {
    $arr = array_map(fn($t) => is_string($t) ? trim($t) : '', $raw);
  } else {
    $arr = [];
  }
  // remove empties, dedupe (case-insensitive), keep original case for display
  $unique = [];
  foreach ($arr as $t) {
    if ($t === '')
      continue;
    $key = mb_strtolower($t);
    $unique[$key] = $unique[$key] ?? $t; // first seen version
  }
  return array_values($unique);
};

$addFromFile = function (string $file, array &$tags) use ($normalizeTags) {
  $raw = @file_get_contents($file);
  if ($raw === false)
    return;
  [$fm] = parse_front_matter($raw);
  $tagList = $normalizeTags($fm['tags'] ?? []);
  foreach ($tagList as $t) {
    $key = mb_strtolower($t);
    $tags[$key]['label'] = $tags[$key]['label'] ?? $t; // preserve display case
    $tags[$key]['count'] = ($tags[$key]['count'] ?? 0) + 1;
  }
};

$tags = [];

// 1) Collections: content/collections/*/*.md
$collRoot = path('content') . '/collections';
if (is_dir($collRoot)) {
  foreach (glob($collRoot . '/*', GLOB_ONLYDIR) as $dir) {
    foreach (glob($dir . '/*.md') as $md) {
      $addFromFile($md, $tags);
    }
  }
}

// 2) Pages: content/pages/*.md
$pagesDir = path('content') . '/pages';
if (is_dir($pagesDir)) {
  foreach (glob($pagesDir . '/*.md') as $md) {
    $addFromFile($md, $tags);
  }
}

// Sort tags alphabetically by label (natural, case-insensitive)
uasort($tags, fn($a, $b) => strnatcasecmp($a['label'], $b['label']));

// Render simple list
// Render tag list with links to /tag/{slug}
ob_start();
?>
<h1>Tags</h1>
<ul class="tags-list">
  <?php if (empty($tags)): ?>
      <li><em>No tags found.</em></li>
  <?php else: ?>
      <?php foreach ($tags as $slug => $info): ?>
          <li>
            <a href="<?= url('/tag/' . urlencode($slug)) ?>">
              <?= htmlspecialchars($info['label'], ENT_QUOTES, 'UTF-8') ?>
            </a>
            <small class="muted">(<?= (int) $info['count'] ?>)</small>
          </li>
      <?php endforeach; ?>
  <?php endif; ?>
</ul>
<?php
$content = ob_get_clean();

render('main', [
  'title' => 'Tags',
  'content' => $content,
  'path' => $path ?? '/tags',
]);