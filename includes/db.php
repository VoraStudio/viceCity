<?php

declare(strict_types=1);

require_once __DIR__ . '/categories.php';

// Variants de color de la coberta de cada entrada (vegeu COVER_STYLES a includes/blog.php).
const BLOG_COVERS = ['lav', 'purple', 'ink'];

const POSTS_FILE = __DIR__ . '/../data/posts.php';
const DB_FILE = __DIR__ . '/../data/blog.sqlite';

/**
 * Obre la BD SQLite del blog (la crea si no existeix) i la deixa sincronitzada
 * amb data/posts.php.
 *
 * Estratègia de sincronització: data/posts.php és la font de la veritat. Es guarda
 * el hash del fitxer a la taula `meta`; quan canvia (o la BD és nova), en una sola
 * transacció es fa upsert per slug de totes les entrades i s'esborren les que ja no
 * hi són. Així, editar posts.php i refrescar ja es veu, i en les peticions normals
 * no s'escriu res a la BD.
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
            views INTEGER NOT NULL DEFAULT 0
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
        'INSERT INTO posts (slug, title, category, excerpt, body, cover, read_minutes, published_at, is_featured)
         VALUES (:slug, :title, :category, :excerpt, :body, :cover, :read_minutes, :published_at, :is_featured)
         ON CONFLICT(slug) DO UPDATE SET
            title = excluded.title,
            category = excluded.category,
            excerpt = excluded.excerpt,
            body = excluded.body,
            cover = excluded.cover,
            read_minutes = excluded.read_minutes,
            published_at = excluded.published_at,
            is_featured = excluded.is_featured'
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
            throw new InvalidArgumentException("Falta el camp «$field» en una entrada de data/posts.php.");
        }
    }

    $slug = $post['slug'];
    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
        throw new InvalidArgumentException("Slug no vàlid: «$slug».");
    }
    if (!array_key_exists($post['category'], BLOG_CATEGORIES)) {
        throw new InvalidArgumentException("Categoria desconeguda «{$post['category']}» a l'entrada «$slug».");
    }
    if (!in_array($post['cover'], BLOG_COVERS, true)) {
        throw new InvalidArgumentException("Coberta desconeguda «{$post['cover']}» a l'entrada «$slug».");
    }
    if (DateTimeImmutable::createFromFormat('!Y-m-d', $post['published_at']) === false) {
        throw new InvalidArgumentException("Data no vàlida a l'entrada «$slug» (format YYYY-MM-DD).");
    }
}
