<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';

const CATALAN_MONTHS = ['gen.', 'febr.', 'març', 'abr.', 'maig', 'juny', 'jul.', 'ag.', 'set.', 'oct.', 'nov.', 'des.'];

const VIEWS_COOKIE = 'vc_v';
const LIKES_COOKIE = 'vc_l';
const COOKIE_MAX_SLUGS = 100;
const COOKIE_LIFETIME = 60 * 60 * 24 * 365;
const BOT_USER_AGENT_PATTERN = '/bot|crawl|spider|slurp|facebookexternalhit|preview|headless|curl|wget|python-requests|httpclient/i';

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
    $sql = 'SELECT slug, title, category, excerpt, cover, read_minutes, published_at, is_featured, likes, views FROM posts';
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

function getCookieSlugs(string $name): array
{
    $raw = $_COOKIE[$name] ?? '';
    if (!is_string($raw) || $raw === '') {
        return [];
    }

    $slugs = array_filter(explode(',', $raw), static fn (string $slug): bool => preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) === 1);

    return array_values(array_unique($slugs));
}

function isHttpsRequest(): bool
{
    return ($_SERVER['HTTPS'] ?? '') !== '' && $_SERVER['HTTPS'] !== 'off';
}

function cookieBasePath(): string
{
    return rtrim(str_replace(chr(92), '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/') . '/';
}

function isSameOrigin(): bool
{
    $fetchSite = $_SERVER['HTTP_SEC_FETCH_SITE'] ?? null;
    if ($fetchSite !== null) {
        return $fetchSite === 'same-origin' || $fetchSite === 'none';
    }

    $origin = $_SERVER['HTTP_ORIGIN'] ?? null;
    if ($origin === null) {
        return true;
    }

    $originHost = parse_url($origin, PHP_URL_HOST);
    $originPort = parse_url($origin, PHP_URL_PORT);
    $expected = $_SERVER['HTTP_HOST'] ?? '';
    $actual = is_string($originHost) ? $originHost . ($originPort !== null ? ':' . $originPort : '') : '';

    return $actual !== '' && strcasecmp($actual, $expected) === 0;
}

function setCookieSlugs(string $name, array $slugs): void
{
    $slugs = array_slice(array_values(array_unique($slugs)), -COOKIE_MAX_SLUGS);

    setcookie($name, implode(',', $slugs), [
        'expires' => time() + COOKIE_LIFETIME,
        'path' => cookieBasePath(),
        'secure' => isHttpsRequest(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    $_COOKIE[$name] = implode(',', $slugs);
}

function isCountableVisit(): bool
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
        return false;
    }

    $purpose = strtolower($_SERVER['HTTP_SEC_PURPOSE'] ?? $_SERVER['HTTP_PURPOSE'] ?? $_SERVER['HTTP_X_MOZ'] ?? '');
    if ($purpose !== '' && (str_contains($purpose, 'prefetch') || str_contains($purpose, 'prerender'))) {
        return false;
    }

    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

    return $userAgent !== '' && preg_match(BOT_USER_AGENT_PATTERN, $userAgent) !== 1;
}

function recordView(PDO $pdo, string $slug): void
{
    if (!isCountableVisit()) {
        return;
    }

    $seen = getCookieSlugs(VIEWS_COOKIE);
    if (in_array($slug, $seen, true)) {
        return;
    }

    $statement = $pdo->prepare('UPDATE posts SET views = views + 1 WHERE slug = :slug');
    $statement->execute(['slug' => $slug]);

    if ($statement->rowCount() > 0) {
        $seen[] = $slug;
        setCookieSlugs(VIEWS_COOKIE, $seen);
    }
}

function hasLiked(string $slug): bool
{
    return in_array($slug, getCookieSlugs(LIKES_COOKIE), true);
}

function toggleLike(PDO $pdo, string $slug): ?array
{
    $liked = getCookieSlugs(LIKES_COOKIE);
    $wasLiked = in_array($slug, $liked, true);

    $sql = $wasLiked
        ? 'UPDATE posts SET likes = MAX(likes - 1, 0) WHERE slug = :slug'
        : 'UPDATE posts SET likes = likes + 1 WHERE slug = :slug';
    $statement = $pdo->prepare($sql);
    $statement->execute(['slug' => $slug]);

    if ($statement->rowCount() === 0) {
        return null;
    }

    $liked = $wasLiked ? array_values(array_diff($liked, [$slug])) : [...$liked, $slug];
    setCookieSlugs(LIKES_COOKIE, $liked);

    $count = $pdo->prepare('SELECT likes FROM posts WHERE slug = :slug');
    $count->execute(['slug' => $slug]);

    $likes = (int) $count->fetchColumn();

    return ['liked' => !$wasLiked, 'likes' => $likes, 'label' => formatCount($likes) . " m'agrada"];
}

function formatCount(int $value): string
{
    if ($value < 1000) {
        return (string) $value;
    }

    if ($value >= 1000000) {
        return preg_replace('/,0$/', '', number_format($value / 1000000, 1, ',', '')) . ' M';
    }

    $thousands = number_format($value / 1000, $value < 10000 ? 1 : 0, ',', '');

    return preg_replace('/,0$/', '', $thousands) . ' k';
}

function likesLabel(int $value): string
{
    return $value . " m'agrada";
}

function viewsLabel(int $value): string
{
    return $value . ($value === 1 ? ' visita' : ' visites');
}

function iconHeart(string $class = 'size-4 shrink-0'): string
{
    return '<svg aria-hidden="true" viewBox="0 0 24 24" class="' . e($class) . '" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" /></svg>';
}

function iconEye(string $class = 'size-4 shrink-0'): string
{
    return '<svg aria-hidden="true" viewBox="0 0 24 24" class="' . e($class) . '" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.06 12.35a1 1 0 0 1 0-.7 10.75 10.75 0 0 1 19.88 0 1 1 0 0 1 0 .7 10.75 10.75 0 0 1-19.88 0" /><circle cx="12" cy="12" r="3" /></svg>';
}

function renderPostStats(array $post, string $class = ''): string
{
    $likes = (int) $post['likes'];
    $views = (int) $post['views'];

    return '<p class="flex items-center gap-4 text-label font-medium text-ink/70' . ($class !== '' ? ' ' . e($class) : '') . '">'
        . '<span class="inline-flex items-center gap-1.5">' . iconHeart()
        . '<span aria-hidden="true">' . e(formatCount($likes)) . '</span><span class="sr-only">' . e(likesLabel($likes)) . '</span></span>'
        . '<span class="inline-flex items-center gap-1.5">' . iconEye()
        . '<span aria-hidden="true">' . e(formatCount($views)) . '</span><span class="sr-only">' . e(viewsLabel($views)) . '</span></span>'
        . '</p>';
}
