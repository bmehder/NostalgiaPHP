<?php
// tools/front-matter-report.php
// A nostalgic, no-login "admin" that reports front-matter across pages & collections.

$root = dirname(__DIR__);
require $root . '/config.php';
require $root . '/functions.php';

date_default_timezone_set(site('timezone') ?: 'UTC');

function h($s)
{
  return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}
function fmt_date($v)
{
  if ($v instanceof DateTime)
    return $v->format('Y-m-d');
  if (is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}/', $v))
    return substr($v, 0, 10);
  return '';
}

function collect_pages(): array
{
  $base = rtrim(path('pages'), '/');
  $out = [];

  if (!is_dir($base))
    return $out;

  $it = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)
  );

  foreach ($it as $f) {
    if (!$f->isFile() || strtolower($f->getExtension()) !== 'md')
      continue;
    $abs = $f->getPathname();
    $rel = trim(str_replace($base, '', $abs), '/'); // e.g. "about.md" or "guides/install.md"
    $rel = preg_replace('/\.md$/i', '', $rel);

    // URL mapping: index.md -> /, foo/index.md -> /foo, foo/bar.md -> /foo/bar
    if ($rel === 'index') {
      $url = '/';
    } elseif (substr($rel, -6) === '/index') {
      $url = '/' . substr($rel, 0, -6);
    } else {
      $url = '/' . $rel;
    }

    [$meta, $md] = parse_front_matter(read_file($abs));
    $out[] = [
      'type' => 'page',
      'file' => $abs,
      'rel' => $rel,
      'url' => $url,
      'meta' => $meta,
      'lastmod' => filemtime($abs),
    ];
  }

  usort($out, fn($a, $b) => strcmp($a['url'], $b['url']));
  return $out;
}

function collect_collections(): array
{
  $collections = array_keys(config()['collections'] ?? []);
  $base = rtrim(path('collections'), '/');
  $out = [];

  foreach ($collections as $c) {
    $dir = $base . '/' . $c;
    $items = [];
    if (is_dir($dir)) {
      foreach (glob($dir . '/*.md') as $file) {
        $slug = basename($file, '.md');
        [$meta, $md] = parse_front_matter(read_file($file));
        $items[] = [
          'type' => 'item',
          'collection' => $c,
          'slug' => $slug,
          'file' => $file,
          'url' => '/' . $c . '/' . $slug,
          'meta' => $meta + ['slug' => $slug],
          'lastmod' => filemtime($file),
        ];
      }

      // optional: apply collection sort rule if defined
      $cfg = config()['collections'][$c] ?? null;
      if ($cfg && isset($cfg['sort'])) {
        [$key, $dirn] = $cfg['sort'];
        usort($items, function ($a, $b) use ($key, $dirn) {
          $av = $a['meta'][$key] ?? null;
          $bv = $b['meta'][$key] ?? null;
          if ($av instanceof DateTime)
            $av = $av->getTimestamp();
          if ($bv instanceof DateTime)
            $bv = $bv->getTimestamp();
          if ($av == $bv)
            return 0;
          $cmp = ($av < $bv) ? -1 : 1;
          return ($dirn === 'desc') ? -$cmp : $cmp;
        });
      }
    }

    $out[$c] = $items;
  }

  ksort($out, SORT_NATURAL | SORT_FLAG_CASE);
  return $out;
}

// --- gather data
$pages = collect_pages();
$cols = collect_collections();

// Summary / issues
$sum = [
  'pages' => count($pages),
  'items' => array_sum(array_map('count', $cols)),
  'drafts' => 0,
  'sitemap_off' => 0,
  'missing_title' => 0,
  'missing_desc' => 0,
];

$issues = []; // list of ["type"=>"page|item", "where"=>"Pages / URL or Collection/slug", "problem"=>"..."]

foreach ($pages as $p) {
  $m = $p['meta'];
  if (!empty($m['draft'])) {
    $sum['drafts']++;
    $issues[] = ['type' => 'page', 'where' => $p['url'], 'problem' => 'draft:true'];
  }
  if (isset($m['sitemap']) && $m['sitemap'] === false) {
    $sum['sitemap_off']++;
    $issues[] = ['type' => 'page', 'where' => $p['url'], 'problem' => 'sitemap:false'];
  }
  if (empty($m['title'])) {
    $sum['missing_title']++;
    $issues[] = ['type' => 'page', 'where' => $p['url'], 'problem' => 'missing title'];
  }
  if (empty($m['description'])) {
    $sum['missing_desc']++;
    $issues[] = ['type' => 'page', 'where' => $p['url'], 'problem' => 'missing description'];
  }
}

foreach ($cols as $c => $items) {
  foreach ($items as $it) {
    $m = $it['meta'];
    $where = $c . '/' . $it['slug'];
    if (!empty($m['draft'])) {
      $sum['drafts']++;
      $issues[] = ['type' => 'item', 'where' => $where, 'problem' => 'draft:true'];
    }
    if (isset($m['sitemap']) && $m['sitemap'] === false) {
      $sum['sitemap_off']++;
      $issues[] = ['type' => 'item', 'where' => $where, 'problem' => 'sitemap:false'];
    }
    if (empty($m['title'])) {
      $sum['missing_title']++;
      $issues[] = ['type' => 'item', 'where' => $where, 'problem' => 'missing title'];
    }
    if (empty($m['description'])) {
      $sum['missing_desc']++;
      $issues[] = ['type' => 'item', 'where' => $where, 'problem' => 'missing description'];
    }
  }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>Front-matter Report — <?= h(site('name')) ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    :root {
      --bg: #fff;
      --ink: #111;
      --muted: #666;
      --line: #e5e5e5;
      --accent: #0a7;
      --pad: .75rem;
      --mono: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
    }

    body {
      font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
      color: var(--ink);
      background: var(--bg);
      margin: 0;
    }

    header,
    section {
      padding: 1rem clamp(.75rem, 2vw, 2rem);
    }

    header {
      border-bottom: 1px solid var(--line);
    }

    h1 {
      margin: 0 0 .25rem;
      font-size: 1.25rem;
    }

    .meta {
      color: var(--muted);
      font-size: .9rem;
    }

    .summary {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: .5rem;
      margin: .75rem 0 0;
    }

    .summary .card {
      border: 1px solid var(--line);
      border-radius: 8px;
      padding: .75rem;
    }

    .summary .n {
      font-weight: 600;
      font-size: 1.2rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin: .5rem 0 1.25rem;
    }

    th,
    td {
      border: 1px solid var(--line);
      padding: .5rem;
      vertical-align: top;
    }

    th {
      background: #fafafa;
      text-align: left;
    }

    code {
      font-family: var(--mono);
      font-size: .9em;
      background: #f6f6f6;
      padding: .1rem .25rem;
      border-radius: 4px;
    }

    .tags {
      color: var(--muted);
    }

    .muted {
      color: var(--muted);
    }

    .problem {
      color: #a00;
    }

    .ok {
      color: var(--accent);
    }

    .wrap {
      white-space: pre-wrap;
    }
  </style>
</head>

<body>
  <header>
    <h1>Front-matter Report</h1>
    <div class="meta"><?= h(site('name')) ?> · Generated <?= date('Y-m-d H:i') ?></div>
    <div class="summary">
      <div class="card">
        <div class="n"><?= $sum['pages'] ?></div>
        <div>Pages</div>
      </div>
      <div class="card">
        <div class="n"><?= $sum['items'] ?></div>
        <div>Collection items</div>
      </div>
      <div class="card">
        <div class="n"><?= $sum['drafts'] ?></div>
        <div>Drafts</div>
      </div>
      <div class="card">
        <div class="n"><?= $sum['sitemap_off'] ?></div>
        <div>Sitemap off</div>
      </div>
      <div class="card">
        <div class="n"><?= $sum['missing_title'] ?></div>
        <div>Missing titles</div>
      </div>
      <div class="card">
        <div class="n"><?= $sum['missing_desc'] ?></div>
        <div>Missing descriptions</div>
      </div>
    </div>
  </header>

  <section>
    <h2>Potential issues</h2>
    <?php if (!$issues): ?>
      <p class="ok">No obvious issues found.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Type</th>
            <th>Where</th>
            <th>Problem</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($issues as $row): ?>
            <tr>
              <td><?= h($row['type']) ?></td>
              <td><code><?= h($row['where']) ?></code></td>
              <td class="problem"><?= h($row['problem']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

  <section>
    <h2>Pages</h2>
    <?php if (!$pages): ?>
      <p class="muted">No pages found.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>URL</th>
            <th>Title</th>
            <th>Description</th>
            <th>Date</th>
            <th>Tags</th>
            <th>Layout</th>
            <th>Draft</th>
            <th>Sitemap</th>
            <th>Last modified</th>
            <th>File</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pages as $p):
            $m = $p['meta']; ?>
            <tr>
              <td><a href="<?= h(url($p['url'])) ?>"><?= h($p['url']) ?></a></td>
              <td><?= h($m['title'] ?? '') ?></td>
              <td class="wrap"><?= h($m['description'] ?? '') ?></td>
              <td><?= h(fmt_date($m['date'] ?? '')) ?></td>
              <td class="tags"><?= h(isset($m['tags']) && is_array($m['tags']) ? implode(', ', $m['tags']) : '') ?></td>
              <td><code><?= h(($m['layout'] ?? 'main')) ?></code></td>
              <td><?= !empty($m['draft']) ? 'true' : '' ?></td>
              <td><?= (isset($m['sitemap']) && $m['sitemap'] === false) ? 'false' : '' ?></td>
              <td><?= date('Y-m-d', $p['lastmod']) ?></td>
              <td><code><?= h(str_replace($root . '/', '', $p['file'])) ?></code></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

  <?php foreach ($cols as $c => $items): ?>
    <section>
      <h2>Collection: <?= h($c) ?></h2>
      <?php if (!$items): ?>
        <p class="muted">No items.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr>
              <th>Slug</th>
              <th>Title</th>
              <th>Description</th>
              <th>Date</th>
              <th>Tags</th>
              <th>Layout</th>
              <th>Draft</th>
              <th>Sitemap</th>
              <th>URL</th>
              <th>Last modified</th>
              <th>File</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $it):
              $m = $it['meta']; ?>
              <tr>
                <td><code><?= h($it['slug']) ?></code></td>
                <td><?= h($m['title'] ?? '') ?></td>
                <td class="wrap"><?= h($m['description'] ?? '') ?></td>
                <td><?= h(fmt_date($m['date'] ?? '')) ?></td>
                <td class="tags"><?= h(isset($m['tags']) && is_array($m['tags']) ? implode(', ', $m['tags']) : '') ?></td>
                <td><code><?= h(($m['layout'] ?? 'main')) ?></code></td>
                <td><?= !empty($m['draft']) ? 'true' : '' ?></td>
                <td><?= (isset($m['sitemap']) && $m['sitemap'] === false) ? 'false' : '' ?></td>
                <td><a href="<?= h(url($it['url'])) ?>"><?= h($it['url']) ?></a></td>
                <td><?= date('Y-m-d', $it['lastmod']) ?></td>
                <td><code><?= h(str_replace($root . '/', '', $it['file'])) ?></code></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>
  <?php endforeach; ?>

</body>

</html>