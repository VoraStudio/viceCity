<?php

declare(strict_types=1);

$secretsFile = __DIR__ . '/secrets.local.php';
$secrets = is_file($secretsFile) ? require $secretsFile : [];

return [
    // PENDENT: substituir per la clau de lloc de reCAPTCHA v3 quan es tingui.
    'site_key' => 'PENDENT_CLAU_DE_SITE',
    'secret_key' => is_string($secrets['recaptcha_secret'] ?? null) ? $secrets['recaptcha_secret'] : '',
    'min_score' => 0.5,
    'action' => 'contacte',
    'hostname' => 'www.vicity.cat',
    'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
];
