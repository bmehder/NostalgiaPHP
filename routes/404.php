<?php
http_response_code(404);

$title = 'Not Found';
$meta = [];
$content = '
  <h1>404 — Page not found</h1>
  <p>Sorry, we couldn’t find that page.</p>
';

render('main', compact('title', 'content', 'path', 'meta'));