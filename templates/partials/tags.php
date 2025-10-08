<?php
// expects: $tags (array of strings)
if (!isset($tags) || !is_array($tags) || !$tags)
  return;
?>
<ul class="tags">
  <?php foreach ($tags as $tag):
    $safe = htmlspecialchars((string) $tag, ENT_QUOTES, 'UTF-8'); ?>
    <li><a href="<?= url('/tag/' . rawurlencode($tag)) ?>"><?= $safe ?></a></li>
  <?php endforeach; ?>
</ul>