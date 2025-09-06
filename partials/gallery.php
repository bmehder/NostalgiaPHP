<?php
// expects: $images = [ ['src' => '...', 'alt' => '...'], ... ]
?>
<div class="gallery auto-fill text-align-center">
  <?php foreach ($images as $img): ?>
    <figure>
      <img class="landscape" src="<?= htmlspecialchars($img['src']) ?>" alt="<?= htmlspecialchars($img['alt'] ?? '') ?>" loading="lazy"
        decoding="async">
      <?php if (!empty($img['alt'])): ?>
        <figcaption><?= htmlspecialchars($img['alt']) ?></figcaption>
      <?php endif; ?>
    </figure>
  <?php endforeach; ?>
</div>