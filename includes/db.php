<?php

declare(strict_types=1);

require_once __DIR__ . '/categories.php';

// Variants de color de la coberta de cada entrada (vegeu COVER_STYLES a includes/blog.php).
const BLOG_COVERS = ['lav', 'purple', 'ink'];

const BLOG_IMAGE_DIR = 'assets/img/blog/';
const BLOG_IMAGE_EXTENSIONS = ['webp', 'jpg', 'jpeg', 'png', 'avif'];
const BLOG_MAX_IMAGES = 12;
const BLOG_MAX_LINKS = 10;

const POSTS_FILE = __DIR__ . '/../data/posts.php';
const DB_FILE = __DIR__ . '/../data/blog.sqlite';

/**
 * Obre la BD SQLite del blog (la crea si no existeix) i la deixa sincronitzada
 * amb data/posts.php.
 */
function getDatabase(): PDO
{
    $pdo = new PDO('sqlite:' . DB_FILE, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5,
    ]);

    createSchema($pdo);
    syncPosts($pdo);

    return $pdo;
}

function createSchema(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            slug TEXT NOT NULL UNIQUE,
            title TEXT NOT NULL,
            category TEXT NOT NULL,
            excerpt TEXT NOT NULL,
            body TEXT NOT NULL,
            cover TEXT NOT NULL,
            read_minutes INTEGER NOT NULL,
            published_at TEXT NOT NULL,
            is_featured INTEGER NOT NULL DEFAULT 0,
            likes INTEGER NOT NULL DEFAULT 0,
            views INTEGER NOT NULL DEFAULT 0,
            images TEXT NOT NULL DEFAULT \'[]\',
            links TEXT NOT NULL DEFAULT \'[]\'
        )'
    );
    $pdo->exec('CREATE TABLE IF NOT EXISTS meta (key TEXT PRIMARY KEY, value TEXT NOT NULL)');
    addMissingPostColumns($pdo);
}

function addMissingPostColumns(PDO $pdo): void
{
    $existing = array_column($pdo->query('PRAGMA table_info(posts)')->fetchAll(), 'name');

    foreach (['likes', 'views'] as $column) {
        if (!in_array($column, $existing, true)) {
            $pdo->exec("ALTER TABLE posts ADD COLUMN $column INTEGER NOT NULL DEFAULT 0");
        }
    }

    foreach (['images', 'links'] as $column) {
        if (!in_array($column, $existing, true)) {
            $pdo->exec("ALTER TABLE posts ADD COLUMN $column TEXT NOT NULL DEFAULT '[]'");
            $pdo->exec("DELETE FROM meta WHERE key = 'posts_hash'");
        }
    }
}

function syncPosts(PDO $pdo): void
{
    $currentHash = md5_file(POSTS_FILE);

    $select = $pdo->prepare('SELECT value FROM meta WHERE key = :key');
    $select->execute(['key' => 'posts_hash']);
    if ($select->fetchColumn() === $currentHash) {
        return;
    }

    $posts = require POSTS_FILE;
    foreach ($posts as $post) {
        validatePost($post);
    }

    $upsert = $pdo->prepare(
        'INSERT INTO posts (slug, title, category, excerpt, body, cover, read_minutes, published_at, is_featured, images, links)
         VALUES (:slug, :title, :category, :excerpt, :body, :cover, :read_minutes, :published_at, :is_featured, :images, :links)
         ON CONFLICT(slug) DO UPDATE SET
            title = excluded.title,
            category = excluded.category,
            excerpt = excluded.excerpt,
            body = excluded.body,
            cover = excluded.cover,
            read_minutes = excluded.read_minutes,
            published_at = excluded.published_at,
            is_featured = excluded.is_featured,
            images = excluded.images,
            links = excluded.links'
    );

    $pdo->beginTransaction();
    try {
        foreach ($posts as $post) {
            $upsert->execute([
                'slug' => $post['slug'],
                'title' => $post['title'],
                'category' => $post['category'],
                'excerpt' => $post['excerpt'],
                'body' => $post['body'],
                'cover' => $post['cover'],
                'read_minutes' => $post['read_minutes'],
                'published_at' => $post['published_at'],
                'is_featured' => empty($post['is_featured']) ? 0 : 1,
                'images' => encodeJsonList(normalizePostImages($post['images'] ?? [])),
                'links' => encodeJsonList(normalizePostLinks($post['links'] ?? [])),
            ]);
        }

        deleteRemovedPosts($pdo, array_column($posts, 'slug'));

        $saveHash = $pdo->prepare('INSERT OR REPLACE INTO meta (key, value) VALUES (:key, :value)');
        $saveHash->execute(['key' => 'posts_hash', 'value' => $currentHash]);

        $pdo->commit();
    } catch (Throwable $error) {
        $pdo->rollBack();
        throw $error;
    }
}

/** Esborra les entrades que ja no són a data/posts.php. */
function deleteRemovedPosts(PDO $pdo, array $slugs): void
{
    if ($slugs === []) {
        $pdo->exec('DELETE FROM posts');
        return;
    }

    $placeholders = implode(',', array_fill(0, count($slugs), '?'));
    $delete = $pdo->prepare("DELETE FROM posts WHERE slug NOT IN ($placeholders)");
    $delete->execute(array_values($slugs));
}

function validatePost(array $post): void
{
    $required = ['slug', 'title', 'category', 'excerpt', 'body', 'cover', 'read_minutes', 'published_at'];
    foreach ($required as $field) {
        if (!isset($post[$field]) || $post[$field] === '') {
            throw new InvalidArgumentException("Falta el camp «{$field}» en una entrada de data/posts.php.");
        }
    }

    $slug = $post['slug'];
    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
        throw new InvalidArgumentException("Slug no vàlid: «{$slug}».");
    }
    if (!array_key_exists($post['category'], BLOG_CATEGORIES)) {
        throw new InvalidArgumentException("Categoria desconeguda «{$post['category']}» a l'entrada «{$slug}».");
    }
    if (!in_array($post['cover'], BLOG_COVERS, true)) {
        throw new InvalidArgumentException("Coberta desconeguda «{$post['cover']}» a l'entrada «{$slug}».");
    }
    if (DateTimeImmutable::createFromFormat('!Y-m-d', $post['published_at']) === false) {
        throw new InvalidArgumentException("Data no vàlida a l'entrada «{$slug}» (format YYYY-MM-DD).");
    }

    validatePostImages($post['images'] ?? [], $slug);
    validatePostLinks($post['links'] ?? [], $slug);
}

function isNonEmptyText(mixed $value, int $maxLength): bool
{
    return is_string($value) && trim($value) !== '' && mb_strlen($value) <= $maxLength;
}

function isValidImageSrc(mixed $src): bool
{
    if (!is_string($src) || strlen($src) > 200) {
        return false;
    }
    if (!preg_match('#^' . preg_quote(BLOG_IMAGE_DIR, '#') . '[A-Za-z0-9._/-]+$#', $src)) {
        return false;
    }
    if (str_contains($src, '..') || str_contains($src, '//')) {
        return false;
    }

    return in_array(strtolower(pathinfo($src, PATHINFO_EXTENSION)), BLOG_IMAGE_EXTENSIONS, true);
}

function isValidLinkUrl(mixed $url): bool
{
    if (!is_string($url) || $url === '' || strlen($url) > 500) {
        return false;
    }
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        return false;
    }
    $parts = parse_url($url);

    return $parts !== false
        && in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)
        && ($parts['host'] ?? '') !== '';
}

function isValidPostImage(mixed $image): bool
{
    return is_array($image)
        && isValidImageSrc($image['src'] ?? null)
        && isNonEmptyText($image['alt'] ?? null, 200)
        && (!isset($image['caption']) || (is_string($image['caption']) && mb_strlen($image['caption']) <= 300));
}

function isValidPostLink(mixed $link): bool
{
    return is_array($link)
        && isNonEmptyText($link['label'] ?? null, 120)
        && isValidLinkUrl($link['url'] ?? null);
}

function validatePostImages(mixed $images, string $slug): void
{
    if (!is_array($images) || !array_is_list($images)) {
        throw new InvalidArgumentException("El camp «images» de l'entrada «{$slug}» ha de ser una llista.");
    }
    if (count($images) > BLOG_MAX_IMAGES) {
        throw new InvalidArgumentException("L'entrada «{$slug}» té massa imatges (màxim " . BLOG_MAX_IMAGES . ').');
    }

    foreach ($images as $index => $image) {
        $position = $index + 1;
        if (!is_array($image) || !isValidImageSrc($image['src'] ?? null)) {
            throw new InvalidArgumentException(
                "Imatge $position de l'entrada «{$slug}»: «src» ha de ser una ruta sota " . BLOG_IMAGE_DIR . ' amb extensió ' . implode(', ', BLOG_IMAGE_EXTENSIONS) . '.'
            );
        }
        if (!isNonEmptyText($image['alt'] ?? null, 200)) {
            throw new InvalidArgumentException("Imatge $position de l'entrada «{$slug}»: falta el text alternatiu «alt» (màxim 200 caràcters).");
        }
        if (isset($image['caption']) && (!is_string($image['caption']) || mb_strlen($image['caption']) > 300)) {
            throw new InvalidArgumentException("Imatge $position de l'entrada «{$slug}»: «caption» ha de ser text de com a màxim 300 caràcters.");
        }
    }
}

function validatePostLinks(mixed $links, string $slug): void
{
    if (!is_array($links) || !array_is_list($links)) {
        throw new InvalidArgumentException("El camp «links» de l'entrada «{$slug}» ha de ser una llista.");
    }
    if (count($links) > BLOG_MAX_LINKS) {
        throw new InvalidArgumentException("L'entrada «{$slug}» té massa enllaços (màxim " . BLOG_MAX_LINKS . ').');
    }

    foreach ($links as $index => $link) {
        $position = $index + 1;
        if (!is_array($link) || !isNonEmptyText($link['label'] ?? null, 120)) {
            throw new InvalidArgumentException("Enllaç $position de l'entrada «{$slug}»: falta «label» (màxim 120 caràcters).");
        }
        if (!isValidLinkUrl($link['url'] ?? null)) {
            throw new InvalidArgumentException("Enllaç $position de l'entrada «{$slug}»: «url» ha de ser una URL http o https vàlida.");
        }
    }
}

function normalizePostImages(array $images): array
{
    return array_map(static function (array $image): array {
        $normalized = ['src' => $image['src'], 'alt' => trim($image['alt'])];
        $caption = isset($image['caption']) ? trim($image['caption']) : '';
        if ($caption !== '') {
            $normalized['caption'] = $caption;
        }

        return $normalized;
    }, array_values($images));
}

function normalizePostLinks(array $links): array
{
    return array_map(
        static fn (array $link): array => ['label' => trim($link['label']), 'url' => $link['url']],
        array_values($links)
    );
}

function encodeJsonList(array $items): string
{
    return json_encode(array_values($items), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
}

function decodeJsonList(mixed $value): array
{
    if (!is_string($value)) {
        return [];
    }
    try {
        $decoded = json_decode($value, true, 8, JSON_THROW_ON_ERROR);
    } catch (JsonException) {
        return [];
    }

    return is_array($decoded) && array_is_list($decoded) ? $decoded : [];
}
