<?php
// partials/sidebar.php
?>
<aside class="sidebar flow">
  <h2>Sidebar</h2>

  <?php
  $placeholder = 'Search this site…';
  include path('partials') . '/search-form.php';
  ?>
  
  <h3>Navigation:</h3>
  <ul>
    <li><a href="<?= url('/') ?>">Home</a></li>
    <li><a href="<?= url('/blog') ?>">Blog</a></li>
    <li><a href="<?= url('/about') ?>">About</a></li>
    <li><a href="https://github.com/bmehder/NostalgiaPHP" target="_blank">GitHub</a></li>
  </ul>
</aside>