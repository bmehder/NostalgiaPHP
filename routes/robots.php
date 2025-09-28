<?php
// routes/robots.php
header('Content-Type: text/plain; charset=utf-8');

// Build an absolute base URL
$cfgBase = trim((string) (config()['site']['base_url'] ?? ''), '/');

// Detect scheme (handle proxies like Render)
$scheme =
  (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) ? $_SERVER['HTTP_X_FORWARDED_PROTO']
    : ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http'));

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// If base_url already absolute, use it; otherwise derive from request
if (preg_match('~^https?://~i', (string) (config()['site']['base_url'] ?? ''))) {
  $absBase = rtrim((string) config()['site']['base_url'], '/');
} else {
  $absBase = rtrim($scheme . '://' . $host . ($cfgBase ? '/' . $cfgBase : ''), '/');
}

// Output robots.txt
echo "User-agent: *\n";
echo "Disallow: /*.md$\n\n";
echo "Sitemap: {$absBase}/sitemap.xml\n";