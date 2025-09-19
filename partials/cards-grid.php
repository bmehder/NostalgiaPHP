<?php
// expects $items
echo '<div class="cards auto-fill" style="margin-block-start: var(--size-3)">';
foreach ($items as $item) {
  // your existing card partial expects $item
  include path('partials') . '/card.php';
}
echo '</div>';