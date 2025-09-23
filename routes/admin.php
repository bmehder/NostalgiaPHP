<?php
// tools/front-matter-report.php
// A nostalgic, no-login "admin" that reports front-matter across pages & collections.

date_default_timezone_set(site('timezone') ?: 'UTC');

// templates/admin.php
// Make a local ROOT for display-only path trimming.
$root = rtrim(dirname(__DIR__), '/');

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

/**
 * Extract tags from front matter (supports: tags, tag, keywords; CSV or array),
 * de-dup case-insensitively, and return a comma-separated string.
 */
function fm_tags(array $meta): string
{
  $collected = [];
  foreach ($meta as $k => $v) {
    $lk = strtolower((string) $k);
    if ($lk !== 'tags' && $lk !== 'tag' && $lk !== 'keywords')
      continue;

    if (is_string($v)) {
      foreach (array_map('trim', explode(',', $v)) as $t) {
        if ($t !== '')
          $collected[] = $t;
      }
    } elseif (is_array($v)) {
      foreach ($v as $t) {
        if (is_string($t)) {
          $t = trim($t);
          if ($t !== '')
            $collected[] = $t;
        }
      }
    }
  }
  // de-dupe case-insensitively, preserve first-seen casing
  $seen = [];
  $out = [];
  foreach ($collected as $t) {
    $key = mb_strtolower($t);
    if (!isset($seen[$key])) {
      $seen[$key] = true;
      $out[] = $t;
    }
  }
  return implode(', ', $out);
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

  .admin {
    body {
      font-family: system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
      color: var(--ink);
      background: var(--bg);
      margin: 0;
    }

    /* header, */
    section {
      padding: 1rem clamp(.75rem, 2vw, 2rem);
    }

    header {
      /* border-bottom: 1px solid var(--line); */
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
  }

  /* Prevent wide cells from forcing horizontal scroll */
  .admin table {
    table-layout: fixed;
  }

  /* columns share space evenly */
  .admin th,
  .admin td {
    overflow-wrap: anywhere;
    word-break: break-word;
  }

  /* Make long links/paths wrap instead of stretching the table */
  .admin td a,
  .admin td code {
    overflow-wrap: anywhere;
    word-break: break-word;
  }

  /* Optional: on narrow screens, hide the heaviest columns */
  @media (width <=50rem) {

    /* Pages table: hide File column */
    section:nth-of-type(3) table th:nth-child(10),
    section:nth-of-type(3) table td:nth-child(10) {
      display: none;
    }

    /* Collections tables: hide File column */
    section:nth-of-type(n+4) table th:nth-last-child(1),
    section:nth-of-type(n+4) table td:nth-last-child(1) {
      display: none;
    }
  }

  /* Clickable headers + sort indicators */
  .admin th.sortable {
    cursor: pointer;
    position: relative;
  }

  .admin th[aria-sort="ascending"]::after {
    content: "▲";
    font-size: .75em;
    margin-inline-start: .25rem;
  }

  .admin th[aria-sort="descending"]::after {
    content: "▼";
    font-size: .75em;
    margin-inline-start: .25rem;
  }

  .admin th[aria-sort="none"]::after {
    content: "";
  }
</style>

<?php include path('partials') . '/head.php'; ?>

<body class="admin">
  <div class="wrapper">
    <?php include path('partials') . '/header.php'; ?>

    <section>
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
    </section>
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
              <th>Template</th>
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
                <td><?= h(fm_tags($m)) ?></td>
                <td><code><?= h(($m['template'] ?? 'main')) ?></code></td>
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
                <th>Template</th>
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
                  <td><?= h(fm_tags($m)) ?></td>
                  <td><code><?= h(($m['template'] ?? 'main')) ?></code></td>
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
    <?php include path('partials') . '/footer.php'; ?>
  </div>
  <script>
    (() => {
      // Make all admin tables sortable by clicking their headers.
      const tables = document.querySelectorAll('.admin table');

      tables.forEach(table => {
        const head = table.tHead && table.tHead.rows[0];
        if (!head) return;

        [...head.cells].forEach((th, idx) => {
          if (th.hasAttribute('data-nosort')) return;
          th.classList.add('sortable');
          th.tabIndex = 0;
          th.setAttribute('role', 'button');
          th.setAttribute('aria-sort', 'none');

          const activate = (ev) => {
            if (ev.type === 'keydown' && !(ev.key === 'Enter' || ev.key === ' ')) return;
            ev.preventDefault();
            sortBy(table, idx, th);
          };

          th.addEventListener('click', activate);
          th.addEventListener('keydown', activate);
        });
      });

      function cellText(row, idx) {
        const cell = row.cells[idx];
        if (!cell) return '';
        // Prefer code/link text when present
        const code = cell.querySelector('code');
        const a = cell.querySelector('a');
        const el = code || a || cell;
        return (el.textContent || '').trim();
      }

      function typedValue(text) {
        // YYYY-MM-DD -> date number
        if (/^\d{4}-\d{2}-\d{2}$/.test(text)) return { t: 'num', v: new Date(text).getTime() };
        // true/false
        if (text === 'true' || text === 'false') return { t: 'num', v: text === 'true' ? 1 : 0 };
        // plain number
        const cleaned = text.replace(/,/g, '');
        if (/^[+-]?\d+(\.\d+)?$/.test(cleaned)) return { t: 'num', v: parseFloat(cleaned) };
        // default: case-insensitive string
        return { t: 'str', v: text.toLowerCase() };
      }

      function sortBy(table, colIdx, th) {
        const tbody = table.tBodies[0];
        if (!tbody) return;

        // Toggle direction
        const current = th.getAttribute('aria-sort');
        const dir = current === 'ascending' ? 'descending' : 'ascending';
        [...table.tHead.rows[0].cells].forEach(h => h.setAttribute('aria-sort', h === th ? dir : 'none'));

        // Build sortable array
        const rows = [...tbody.rows].map((row, i) => {
          const val = typedValue(cellText(row, colIdx));
          return { row, val, i }; // keep index for stability if needed
        });

        rows.sort((a, b) => {
          if (a.val.t === 'num' && b.val.t === 'num') {
            return a.val.v - b.val.v;
          }
          if (a.val.v < b.val.v) return -1;
          if (a.val.v > b.val.v) return 1;
          return 0;
        });

        if (dir === 'descending') rows.reverse();

        const frag = document.createDocumentFragment();
        rows.forEach(r => frag.appendChild(r.row));
        tbody.appendChild(frag);
      }
    })();
  </script>

</body>

</html>