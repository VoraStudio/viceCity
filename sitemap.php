<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/blog.php';
require_once __DIR__ . '/includes/site.php';

$entries = [
    ['loc' => siteUrl(), 'lastmod' => null],
    ['loc' => siteUrl('avis-legal.html'), 'lastmod' => null],
    ['loc' => siteUrl('privacitat.html'), 'lastmod' => null],
    ['loc' => siteUrl('cookies.html'), 'lastmod' => null],
];

if (siteConfig()['blog_indexable']) {
    $entries[] = ['loc' => siteUrl('blog.php'), 'lastmod' => null];

    foreach (getPosts(getDatabase(), null) as $post) {
        $entries[] = ['loc' => siteUrl(getArticleUrl($post['slug'])), 'lastmod' => $post['published_at']];
    }
}

$xml = new XMLWriter();
$xml->openMemory();
$xml->setIndent(true);
$xml->setIndentString('  ');
$xml->startDocument('1.0', 'UTF-8');
$xml->startElement('urlset');
$xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

foreach ($entries as $entry) {
    $xml->startElement('url');
    $xml->writeElement('loc', $entry['loc']);
    if ($entry['lastmod'] !== null) {
        $xml->writeElement('lastmod', $entry['lastmod']);
    }
    $xml->endElement();
}

$xml->endElement();
$xml->endDocument();

header('Content-Type: application/xml; charset=utf-8');

echo $xml->outputMemory();
