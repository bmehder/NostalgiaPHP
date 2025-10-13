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
      'https://nostalgiaphp.netlify.app',
      'http://localhost:5500',
      'http://localhost:5173',
    ],
  ],
  'collections' => [
    'blog' => [
      'permalink' => '/blog/{slug}',
      'list_url' => '/blog',
      'sort' => ['date', 'desc'],
      'per_page' => 9,
      'description' => 'Notes, updates, and long-form posts from the NostalgiaPHP project.',
    ],
    'dox' => [
      'permalink' => '/dox/{slug}',
      'list_url' => '/dox',
      'sort' => ['date', 'desc'],
      'description' => 'Guides and documentation for getting the most out of NostalgiaPHP.',
    ],
  ],
];