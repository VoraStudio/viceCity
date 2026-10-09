<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/site.php';

header('Content-Type: text/plain; charset=utf-8');

echo "User-agent: *\n";
echo siteIsConfigured() ? "Allow: /\n" : "Disallow: /\n";
echo "\nSitemap: " . siteUrl('sitemap.xml') . "\n";
