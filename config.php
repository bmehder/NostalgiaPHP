<?php
return [
  'site' => [
    'name' => 'NostalgiaPHP',
    'base_url' => '/', // adjust if in subfolder
    'timezone' => 'Europe/London',
  ],
  'paths' => [
    'content' => __DIR__ . '/content',
    'pages' => __DIR__ . '/content/pages',
    'collections' => __DIR__ . '/content/collections',
    'templates' => __DIR__ . '/templates',
    'partials' => __DIR__ . '/partials',
    'static' => '/static',
  ],
  'collections' => [
    'blog' => [
      'permalink' => '/blog/{slug}',
      'list_url' => '/blog',
      'sort' => ['date','desc'],
    ],
    'dox' => [
      'permalink' => '/dox/{slug}',
      'list_url' => '/dox',
      'sort' => ['date', 'desc'],
    ],
  ],
];
