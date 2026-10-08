<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

const CATALAN_MONTHS = ['gen.', 'febr.', 'març', 'abr.', 'maig', 'juny', 'jul.', 'ag.', 'set.', 'oct.', 'nov.', 'des.'];

// Classes de Tailwind de cada variant de coberta. Han d'aparèixer senceres perquè
// Tailwind (mode navegador) les detecti al DOM.
const COVER_STYLES = [
    'lav' => [
        'background' => 'bg-white',
        'badge' => 'border border-lav-300 bg-lav-100/60 text-purple-700',
        'circle' => 'bg-lav-100/60',
    ],
    'purple' => [
        'background' => 'bg-purple-700',
        'badge' => 'border border-white/40 bg-white/20 text-white',
        'circle' => 'bg-white/15',
    ],
    'ink' => [
        'background' => 'bg-ink',
        'badge' => 'border border-white/40 bg-white/20 text-white',
        'circle' => 'bg-white/15',
    ],
];

/** Escapa una cadena per imprimir-la a l'HTML. */
function e(string|int $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Data ISO (2026-09-18) a format curt en català (18 set. 2026). */
function formatDateCatalan(string $isoDate): string
{
    $date = new DateTimeImmutable($isoDate);

    return $date->format('j') . ' ' . CATALAN_MONTHS[(int) $date->format('n') - 1] . ' ' . $date->format('Y');
}

/** Llista d'entrades, de la més recent a la més antiga; filtrada per categoria si se'n passa una. */
function getPosts(PDO $pdo, ?string $category): array
{
    $sql = 'SELECT slug, title, category, excerpt, cover, read_minutes, published_at, is_featured FROM posts';
    $params = [];
    if ($category !== null) {
        $sql .= ' WHERE category = :category';
        $params['category'] = $category;
    }
    $sql .= ' ORDER BY published_at DESC, id DESC';

    $statement = $pdo->prepare($sql);
    $statement->execute($params);

    return $statement->fetchAll();
}

function getPostBySlug(PDO $pdo, string $slug): ?array
{
    $statement = $pdo->prepare('SELECT * FROM posts WHERE slug = :slug');
    $statement->execute(['slug' => $slug]);
    $post = $statement->fetch();

    return $post === false ? null : $post;
}

/** Categoria demanada per la URL, només si existeix; qualsevol altra cosa vol dir «totes». */
function getRequestedCategory(): ?string
{
    $category = $_GET['categoria'] ?? null;
    if (!is_string($category) || !array_key_exists($category, BLOG_CATEGORIES)) {
        return null;
    }

    return $category;
}

function getArticleUrl(string $slug): string
{
    return 'articulo.php?slug=' . rawurlencode($slug);
}
