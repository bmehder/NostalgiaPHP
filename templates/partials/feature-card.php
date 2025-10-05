<article class="card">
  <div class="card-text">
    <h3 class="card-title">
      <span class="icon accent" aria-hidden="true">
        <?= $icon ?? '' ?>
      </span>
      <?= htmlspecialchars($title ?? '') ?>
    </h3>
    <p class="card-excerpt"><?= $excerpt ?? '' ?></p>
  </div>
</article>