<?php
$ENV = getenv('APP_ENV') ?: 'dev';

if ($ENV !== 'prod'): ?>
    <div style="
    position:fixed; inset:auto 0 0 auto; z-index:9999;
    background:#0ea5e9; color:#fff; padding:.25rem .5rem;
    font:12px/1 system-ui; border-top-left-radius:.5rem;">
      <?= strtoupper($ENV) ?>
    </div>
<?php endif; ?>