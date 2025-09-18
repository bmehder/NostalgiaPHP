<?php
// partials/sidebar.php

// ---- config
$sidebar_heading = 'Explore';
$recent_limit = 5;

// ---- minimal front matter reader (title + date only)
$read_meta = function (string $file): array {
  $meta = ['title' => null, 'date' => null];
  $s = @file_get_contents($file);
  if ($s === false || strncmp($s, "---", 3) !== 0)
    return $meta;
  $end = strpos($s, "\n---", 3);
  if ($end === false)
    return $meta;
  $yaml = substr($s, 3, $end - 3);
  foreach (preg_split('/\R/', $yaml) as $line) {
    if (!str_contains($line, ':'))
      continue;
    [$k, $v] = array_map('trim', explode(':', $line, 2));
    if ($k === 'title' && $v !== '')
      $meta['title'] = trim($v, " \"'");
    if ($k === 'date' && $v !== '')
      $meta['date'] = trim($v, " \"'");
  }
  return $meta;
};

// ---- collect recent items across collections
$items = [];
$collRoot = path('content') . '/collections';
if (is_dir($collRoot)) {
  foreach (glob($collRoot . '/*', GLOB_ONLYDIR) as $collDir) {
    $collection = basename($collDir);
    foreach (glob($collDir . '/*.md') as $f) {
      $slug = basename($f, '.md');
      $meta = $read_meta($f);
      $title = $meta['title'] ?: ucwords(str_replace('-', ' ', $slug));
      $date = $meta['date'] ?: date('Y-m-d', filemtime($f));
      $ts = @strtotime($meta['date'] ?? '') ?: filemtime($f);
      $items[] = [
        'url' => url('/' . $collection . '/' . $slug),
        'title' => $title,
        'date' => $date,
        'ts' => $ts,
      ];
    }
  }
}
usort($items, fn($a, $b) => $b['ts'] <=> $a['ts']);
$recent = array_slice($items, 0, $recent_limit);
?>

<aside class="sidebar flow">
  <h2><?= htmlspecialchars($sidebar_heading) ?></h2>

  <?php
  $placeholder = 'Search this site…';
  include path('partials') . '/search-form.php';
  ?>

  <h3>Recent Items</h3>
  <ul>
    <?php if ($recent):
      foreach ($recent as $it): ?>
        <li>
          <a href="<?= htmlspecialchars($it['url']) ?>"><?= htmlspecialchars($it['title']) ?></a><br />
          <small class="muted"><?= htmlspecialchars($it['date']) ?></small>
        </li>
      <?php endforeach; else: ?>
      <li><em>No items yet.</em></li>
    <?php endif; ?>
  </ul>
</aside>