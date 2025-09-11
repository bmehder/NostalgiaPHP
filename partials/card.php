<?php
// expects: $item (from list_collection), $collection (string)
$href = url("/{$collection}/{$item['slug']}");
$title = htmlspecialchars($item['meta']['title'] ?? $item['slug']);
$date = '';
if (!empty($item['meta']['date']) && $item['meta']['date'] instanceof DateTime) {
  $date = $item['meta']['date']->format('Y-m-d');
} elseif (!empty($item['meta']['date'])) {
  $date = htmlspecialchars((string) $item['meta']['date']);
}

// Safely build excerpt
$excerpt = $item['meta']['excerpt'] ?? '';
if (!$excerpt && !empty($item['html'])) {
  $excerpt = excerpt_from_html($item['html'], 180);
}

// Image
if (!empty($item['meta']['image'])) {
  $image = $item['meta']['image'];
  if ($image[0] === '/') {
    // treat leading slash as relative to site root
    $image = url($image);
  }
} else {
  $image = '';
}
?>
<article class="card">
  <?php if ($image): ?>
    <a href="<?= $href ?>"><img class="card-image" src="<?= $image ?>" alt="<?= $title ?>"></a>
  <?php endif; ?>
  <div class="card-text">
    <h3 class="card-title"><a href="<?= $href ?>"><?= $title ?></a></h3>
    <?php if ($date): ?>
      <p class="card-meta"><small><?= $date ?></small></p>
    <?php endif; ?>
    <?php if ($excerpt): ?>
      <p class="card-excerpt"><?= htmlspecialchars($excerpt) ?></p>
    <?php endif; ?>
    <p class="card-cta"><a class="btn" href="<?= $href ?>">Read more →</a></p>
  </div>
</article>