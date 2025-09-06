<?php
// expects: $title (string), $subtitle (string, optional), $image (optional)
?>
<section class="hero">
  <div class="hero-grid">
    <?php if (!empty($image)): ?>
      <img class="hero-bg widescreen" src="<?= $image ?>" alt="">
    <?php endif; ?>
    <div class="hero-content">
      <h1><?= htmlspecialchars($title) ?></h1>
      <?php if (!empty($subtitle)): ?>
        <p class="hero-sub"><?= htmlspecialchars($subtitle) ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>