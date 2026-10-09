<?php

declare(strict_types=1);

require_once __DIR__ . '/blog.php';

const CONTACT_MIN_INTERVAL = 10;
const CONTACT_MIN_FILL_SECONDS = 3;

function startContactSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    session_name('vc_contact');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => cookieBasePath(),
        'secure' => isHttpsRequest(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function getContactToken(): string
{
    if (!isset($_SESSION['contact_csrf']) || !is_string($_SESSION['contact_csrf'])) {
        $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
        $_SESSION['contact_token_issued_at'] = microtime(true);
    }

    return $_SESSION['contact_csrf'];
}

function rotateContactToken(): string
{
    $_SESSION['contact_csrf'] = bin2hex(random_bytes(32));
    $_SESSION['contact_token_issued_at'] = microtime(true);

    return $_SESSION['contact_csrf'];
}

function isValidContactToken(mixed $token): bool
{
    $expected = $_SESSION['contact_csrf'] ?? null;

    return is_string($expected) && is_string($token) && $token !== '' && hash_equals($expected, $token);
}

function isContactSubmittedTooFast(): bool
{
    $issuedAt = $_SESSION['contact_token_issued_at'] ?? null;

    return (is_float($issuedAt) || is_int($issuedAt)) && microtime(true) - $issuedAt < CONTACT_MIN_FILL_SECONDS;
}

function isContactRateLimited(): bool
{
    $last = $_SESSION['last_submit_time'] ?? null;

    return is_int($last) && time() - $last < CONTACT_MIN_INTERVAL;
}

function markContactSubmitted(): void
{
    $_SESSION['last_submit_time'] = time();
}
