<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/blog.php';
require_once __DIR__ . '/includes/http.php';

function respond(int $status, array $payload, string $redirectTo): never
{
    http_response_code($status);
    header('Cache-Control: no-store');

    if (wantsJson()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($status < 400) {
        header('Location: ' . $redirectTo, true, 303);
        exit;
    }

    header('Content-Type: text/plain; charset=utf-8');
    echo $payload['error'] ?? 'Error';
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    respond(405, ['error' => 'Mètode no permès.'], 'blog.php');
}

if (!isSameOrigin()) {
    respond(403, ['error' => 'Origen no permès.'], 'blog.php');
}

$slug = $_POST['slug'] ?? null;
if (!is_string($slug) || preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) !== 1) {
    respond(400, ['error' => 'Article no vàlid.'], 'blog.php');
}

$result = toggleLike(getDatabase(), $slug);
if ($result === null) {
    respond(404, ['error' => 'Article no trobat.'], 'blog.php');
}

respond(200, $result, getArticleUrl($slug));
