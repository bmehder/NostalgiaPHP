<?php
return [
  'site' => [
    'name' => 'NostalgiaPHP',
    'base_url' => '/',           // adjust if in subfolder
    'timezone' => 'Europe/London',
  ],
  'api' => [
    'enabled' => true,
    'cors_allowlist' => [
      'https://svelte.dev',
      'https://nostalgiaphp.onrender.com',
      // 'http://localhost:5173',
    ],
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
    'projects' => [
      'permalink' => '/projects/{slug}',
      'list_url' => '/projects',
      'sort' => ['date', 'desc'],
    ],
  ],
];