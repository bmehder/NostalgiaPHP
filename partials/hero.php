<?php
// expects: $hero_title (string), $hero_subtitle (string, optional), $hero_image (optional)
?>
<section class="hero">
  <div class="hero-grid">
    <?php if (!empty($hero_image)): ?>
      <img class="hero-bg widescreen" src="<?= $hero_image ?>" alt="">
    <?php endif; ?>
    <div class="hero-content">
      <div class="h1"><?= htmlspecialchars($hero_title) ?></div>
      <?php if (!empty($hero_subtitle)): ?>
        <p class="hero-sub"><?= htmlspecialchars($hero_subtitle) ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>