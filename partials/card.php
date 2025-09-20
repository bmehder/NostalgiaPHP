<?php
// expects: $item (from list_collection), $collection (string)

$collection = $collection ?? ($item['collection'] ?? '');
$slug = (string) ($item['slug'] ?? '');
$href = $collection && $slug ? url("/{$collection}/{$slug}") : ($item['url'] ?? '#');

$slug  = (string)($item['slug'] ?? '');
$href  = url('/' . $collection . '/' . rawurlencode($slug));

$titleRaw = $item['meta']['title'] ?? $item['frontmatter']['title'] ?? $slug;
$title    = htmlspecialchars($titleRaw, ENT_QUOTES, 'UTF-8');

// Date (supports DateTime or string)
$date = '';
$fmDate = $item['meta']['date'] ?? $item['frontmatter']['date'] ?? null;
if ($fmDate instanceof DateTime) {
  $date = $fmDate->format('Y-m-d');
} elseif (!empty($fmDate)) {
  $date = htmlspecialchars((string)$fmDate, ENT_QUOTES, 'UTF-8');
}

// Excerpt (front matter or derived from HTML/content)
$excerpt = $item['meta']['excerpt'] ?? $item['frontmatter']['excerpt'] ?? '';
if (!$excerpt) {
  $html = $item['html'] ?? $item['content'] ?? '';
  if ($html) $excerpt = excerpt_from_html($html, 180);
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
<article class="card bg-white">
  <?php if ($image): ?>
    <a href="<?= $href ?>"><img class="card-image square" src="<?= htmlspecialchars($image, ENT_QUOTES, 'UTF-8') ?>" alt="<?= $title ?>" loading="lazy"></a>
  <?php endif; ?>

  <div class="card-text">
    <h3 class="card-title"><a href="<?= $href ?>"><?= $title ?></a></h3>

    <?php if ($date): ?>
      <p class="card-meta"><small><?= $date ?></small></p>
    <?php endif; ?>

    <?php if ($excerptEsc): ?>
      <p class="card-excerpt"><?= $excerptEsc ?></p>
    <?php endif; ?>

    <?php if (!empty($tags) && is_array($tags)): ?>
      <ul class="tags">
        <?php foreach ($tags as $tag): $t = htmlspecialchars((string)$tag, ENT_QUOTES, 'UTF-8'); ?>
          <li><a href="<?= url('/tag/' . rawurlencode($tag)) ?>"><?= $t ?></a></li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <p class="card-cta"><a class="btn" href="<?= $href ?>">Read more →</a></p>
  </div>
</article>