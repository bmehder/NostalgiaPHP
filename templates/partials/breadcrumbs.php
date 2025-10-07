<?php
// breadcrumbs.php — only renders if on a collection item

$curPath = $path ?? ($_SERVER['REQUEST_URI'] ?? '/');
$parts = array_values(array_filter(explode('/', trim($curPath, '/'))));
$collections = array_keys(config()['collections'] ?? []);

$isItem = (count($parts) === 2) && in_array($parts[0], $collections, true);

if ($isItem) {
  [$collection, $slug] = $parts;
  $item = function_exists('load_collection_item') ? load_collection_item($collection, $slug) : null;
  $title = $item['meta']['title'] ?? ucwords(str_replace(['-', '_'], ' ', $slug));
  ?>
  <nav class="breadcrumbs" aria-label="Breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">
    <ol>
      <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <a itemprop="item" href="<?= url('/') ?>"><span itemprop="name">Home</span></a>
        <meta itemprop="position" content="1">
      </li>
      <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <a itemprop="item" href="<?= url('/' . $collection) ?>">
          <span itemprop="name"><?= htmlspecialchars(ucfirst($collection), ENT_QUOTES, 'UTF-8') ?></span>
        </a>
        <meta itemprop="position" content="2">
      </li>
      <li aria-current="page" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">
        <span itemprop="name"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></span>
        <meta itemprop="position" content="3">
      </li>
    </ol>
  </nav>
  <?php
}
?>