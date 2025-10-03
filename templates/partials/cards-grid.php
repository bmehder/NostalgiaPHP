<?php
// expects $items and $collection
echo '<div class="cards auto-fill align-items-stretch" style="margin-block-start: var(--size-3)" data-appear-children data-stagger="128">';
foreach ($items as $item) {
  // your existing card partial expects $item
  include path('partials') . '/card.php';
}
echo '</div>';