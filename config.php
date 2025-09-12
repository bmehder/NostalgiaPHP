<?php
return [
  'site' => [
    'name' => 'NostalgiaPHP',
    'base_url' => '/',           // adjust if in subfolder
    'timezone' => 'Europe/London',
  ],
  'collections' => [
    'blog' => [
      'permalink' => '/blog/{slug}',
      'list_url' => '/blog',
      'sort' => ['date', 'desc'],
    ],
    'dox' => [
      'permalink' => '/dox/{slug}',
      'list_url' => '/dox',
      'sort' => ['date', 'desc'],
    ],
  ],
];