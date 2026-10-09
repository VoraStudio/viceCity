<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/blog.php';
require_once __DIR__ . '/includes/http.php';
require_once __DIR__ . '/includes/contact-security.php';
require_once __DIR__ . '/includes/recaptcha.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    header('Allow: GET');
    respondJson(405, ['ok' => false, 'message' => 'Mètode no permès.']);
}

if (!isSameOrigin()) {
    respondJson(403, ['ok' => false, 'message' => 'Origen no permès.']);
}

startContactSession();

respondJson(200, ['ok' => true, 'token' => getContactToken(), 'recaptcha' => recaptchaSiteKey()]);
