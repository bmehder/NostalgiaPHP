<?php
require __DIR__ . '/../functions.php';

$drafts = [];

// Pages
foreach (glob(path('pages') . '/**/*.md') as $file) {
  [$meta, $md] = parse_front_matter(read_file($file));
  if (!empty($meta['draft'])) {
    $drafts[] = "Page: " . str_replace(path('pages') . '/', '', $file);
  }
}

// Collections
foreach (array_keys(config()['collections'] ?? []) as $c) {
  $dir = path('collections') . "/$c";
  foreach (glob("$dir/*.md") as $file) {
    [$meta, $md] = parse_front_matter(read_file($file));
    if (!empty($meta['draft'])) {
      $drafts[] = "Collection [$c]: " . basename($file);
    }
  }
}

if ($drafts) {
  echo "Drafts found:\n";
  foreach ($drafts as $d) {
    echo " - $d\n";
  }
} else {
  echo "No drafts found.\n";
}