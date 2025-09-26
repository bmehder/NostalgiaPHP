<?php
$ENV = getenv('APP_ENV') ?: 'dev';

if ($ENV !== 'prod'): ?>
  <div style="
      position: fixed;
      inset: auto 0 0 auto;
      z-index: 9999;
      background: var(--accent);
      color: #fff;
      padding-block: var(--size-0-2-5);
      padding-inline: var(--size-0-5);
      font: 12px/1 system-ui;
      border-top-left-radius: var(--size-0-5);">
    <?= strtoupper($ENV) ?>
  </div>
<?php endif; ?>