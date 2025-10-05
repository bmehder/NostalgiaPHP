<?php
// partials/search-form.php
// Reusable server-side search form.
// Accepts optional variables before include:
//   $q           (string) current query to prefill
//   $action      (string) form action (default: /search)
//   $placeholder (string) input placeholder
//   $compact     (bool)   if true, uses smaller spacing/classes

$q = isset($q) ? (string) $q : (string) ($_GET['q'] ?? '');
$action = isset($action) ? (string) $action : url('/search');
$placeholder = isset($placeholder) ? (string) $placeholder : 'Search…';
$compact = !empty($compact);

$h = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
?>
<search>
  <form class="search-form flex" method="get" action="<?= $h($action) ?>" role="search">
    <label class="visually-hidden" for="q">Search</label>
    <input id="q" class="flex" type="search" name="q" value="<?= $h($q) ?>" placeholder="<?= $h($placeholder) ?>"
      autocomplete="off" />
    <button type="submit" class="margin-0">Search</button>
  </form>
</search>