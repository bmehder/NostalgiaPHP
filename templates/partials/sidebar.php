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
          <small><?= htmlspecialchars(date('M j, Y', strtotime($it['date']))) ?></small>
        </li>
      <?php endforeach; else: ?>
      <li><em>No items yet.</em></li>
    <?php endif; ?>
  </ul>
  <!-- ——— TAGS (simple list) ——— -->
  <?php
  // Collect tags from front matter across collections + pages
  $__tags = [];

  $__addFromFM = function (array $fm) use (&$__tags) {
    foreach ($fm as $k => $v) {
      $lk = strtolower((string) $k);
      if ($lk !== 'tags' && $lk !== 'tag' && $lk !== 'keywords')
        continue;

      // Normalize to array of strings
      $arr = is_string($v) ? array_map('trim', explode(',', $v)) : (array) $v;
      foreach ($arr as $t) {
        if (!is_string($t))
          continue;
        $t = trim($t);
        if ($t === '')
          continue;

        $slug = strtolower($t); // URL slug
        $__tags[$slug]['label'] = $__tags[$slug]['label'] ?? $t; // keep first-seen casing
        $__tags[$slug]['count'] = ($__tags[$slug]['count'] ?? 0) + 1;
      }
    }
  };

  // Scan collections: content/collections/*/*.md
  $__collRoot = path('content') . '/collections';
  if (is_dir($__collRoot)) {
    foreach (glob($__collRoot . '/*', GLOB_ONLYDIR) as $__dir) {
      foreach (glob($__dir . '/*.md') as $__md) {
        $raw = @file_get_contents($__md);
        if ($raw === false)
          continue;
        [$fm] = parse_front_matter($raw);
        $__addFromFM((array) $fm);
      }
    }
  }

  // Scan pages: content/pages/*.md
  $__pagesDir = path('content') . '/pages';
  if (is_dir($__pagesDir)) {
    foreach (glob($__pagesDir . '/*.md') as $__md) {
      $raw = @file_get_contents($__md);
      if ($raw === false)
        continue;
      [$fm] = parse_front_matter($raw);
      $__addFromFM((array) $fm);
    }
  }

  // Sort A→Z and cap to 20 items
  uasort($__tags, fn($a, $b) => strnatcasecmp($a['label'] ?? '', $b['label'] ?? ''));
  $__tags = array_slice($__tags, 0, 20, true);
  ?>

  <section class="sidebar-section">
    <h3 class="sidebar-heading">Tags</h3>
    <?php if (empty($__tags)): ?>
      <p>No tags yet.</p>
    <?php else: ?>
      <ul class="tags-list">
        <?php foreach ($__tags as $slug => $info): ?>
          <li>
            <a href="<?= url('/tag/' . urlencode($slug)) ?>">
              <?= htmlspecialchars($info['label'] ?? $slug, ENT_QUOTES, 'UTF-8') ?>
            </a>
            <small>(<?= (int) ($info['count'] ?? 0) ?>)</small>
          </li>
        <?php endforeach; ?>
      </ul>
      <!-- <p class="tag-cloud-more"><a href="<?= url('/tags') ?>">View all tags →</a></p> -->
    <?php endif; ?>
  </section>
</aside>