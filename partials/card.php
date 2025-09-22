<?php
// expects: $item (from list_collection), $collection (string)

$collection = $collection ?? ($item['collection'] ?? '');
$slug = (string) ($item['slug'] ?? '');
$href = $collection && $slug ? url("/{$collection}/{$slug}") : ($item['url'] ?? '#');

$slug = (string) ($item['slug'] ?? '');
$href = url('/' . $collection . '/' . rawurlencode($slug));

$titleRaw = $item['meta']['title'] ?? $item['frontmatter']['title'] ?? $slug;
$title = htmlspecialchars($titleRaw, ENT_QUOTES, 'UTF-8');

// Date (supports DateTime or string)
$date = '';
$fmDate = $item['meta']['date'] ?? $item['frontmatter']['date'] ?? null;
if ($fmDate instanceof DateTime) {
  $date = $fmDate->format('Y-m-d');
} elseif (!empty($fmDate)) {
  $date = htmlspecialchars((string) $fmDate, ENT_QUOTES, 'UTF-8');
}

// Excerpt (front matter or derived from HTML/content)
$excerpt = $item['meta']['excerpt'] ?? $item['frontmatter']['excerpt'] ?? '';
if (!$excerpt) {
  $html = $item['html'] ?? $item['content'] ?? '';
  if ($html)
    $excerpt = excerpt_from_html($html, 180);
}
$excerptEsc = $excerpt !== '' ? htmlspecialchars($excerpt, ENT_QUOTES, 'UTF-8') : '';

// Tags (normalized to array elsewhere)
$tags = $item['frontmatter']['tags'] ?? $item['meta']['tags'] ?? [];

// Image
$image = $item['meta']['image'] ?? $item['frontmatter']['image'] ?? '';
if (is_string($image) && $image !== '' && $image[0] === '/') {
  // leading slash → treat as site-rooted path
  $image = url($image);
}
?>
<article class="card flex flex-direction-column bg-white">
  <?php if ($image): ?>
    <a href="<?= $href ?>"><img class="card-image square" src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>"
        alt="<?= $title ?>" loading="lazy"></a>
  <?php endif; ?>

  <div class="card-text flex flex-direction-column flex-1 gap-1-5">
    <div>
      <?php
      // Normalize tags to an array (accepts $tags, meta.frontmatter tags|tag|keywords, CSV or array)
      $tags = $tags ?? null;

      if (!is_array($tags)) {
        $fm = $item['meta'] ?? ($item['frontmatter'] ?? []);
        $raw = $tags ?? ($fm['tags'] ?? ($fm['tag'] ?? ($fm['keywords'] ?? [])));

        if (is_string($raw)) {
          // split CSV like "php, retro, tips"
          $tags = array_values(array_filter(array_map('trim', preg_split('/\s*,\s*/', $raw))));
        } elseif (is_array($raw)) {
          $tags = array_values(array_filter(array_map(
            fn($t) => is_string($t) ? trim($t) : '',
            $raw
          )));
        } else {
          $tags = [];
        }
      }
      ?>
      <h3 class="card-title"><a href="<?= $href ?>"><?= $title ?></a></h3>
      <?php if ($date): ?>
        <p class="card-meta muted"><small><?= $date ?></small></p>
      <?php endif; ?>
      <?php if ($excerptEsc): ?>
        <p class="card-excerpt"><?= $excerptEsc ?></p>
      <?php endif; ?>
      <?php if (!empty($tags) && is_array($tags)): ?>
        <ul class="tags">
          <?php foreach ($tags as $tag):
            $t = htmlspecialchars((string) $tag, ENT_QUOTES, 'UTF-8'); ?>
            <li><a href="<?= url('/tag/' . rawurlencode($tag)) ?>"><?= $t ?></a></li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </div>

    <p class="card-cta" style="margin-block-start: auto;">
      <a class="button" href="<?= $href ?>">Read more →</a>
    </p>
  </div>
</article>