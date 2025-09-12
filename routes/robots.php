<?php
// app/routes/robots.php
header('Content-Type: text/plain; charset=utf-8');

// Disallow raw markdown crawling; link sitemap
echo "User-agent: *\n";
echo "Disallow: /*.md$\n";
echo "\n";
echo "Sitemap: " . url('/sitemap.xml') . "\n";