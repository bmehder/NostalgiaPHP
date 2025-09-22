<?php include path('partials') . '/env-badge.php'; ?>
<footer>
  <div class="outer">
    <div class="inner">
      <div class="content spread-apart">
        &copy; <?= date('Y') ?> <?= htmlspecialchars(site('name')) ?>. All rights reserved.
        <button class="no-padding" onclick="window.scrollTo(0, 0)">
          ⬆ Back to Top
        </button>
      </div>
    </div>
  </div>
</footer>