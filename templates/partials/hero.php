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
      <?php if (!empty($hero_button_text)): ?>
        <?php $hero_button_icon = $meta['hero_button_icon'] ?? null; ?>
        <div class="hero-button">
          <a href="<?= $hero_button_link ?? '#' ?>" class="button">
            <?php if ($hero_button_icon): ?>
              <?= icon_svg($hero_button_icon, 'icon icon--btn', 18) ?>
            <?php endif; ?>
            <span><?= htmlspecialchars($hero_button_text, ENT_QUOTES, 'UTF-8') ?></span>
          </a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>