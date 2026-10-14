<?php

declare(strict_types=1);

require_once __DIR__ . '/site.php';

return [
    // Claus de reCAPTCHA v3: només de l'entorn (o .env). Si falten, reCAPTCHA queda desactivat.
    'site_key' => siteEnvValue('VICITY_RECAPTCHA_SITE_KEY') ?? '',
    'secret_key' => siteEnvValue('VICITY_RECAPTCHA_SECRET') ?? '',
    'min_score' => 0.5,
    'action' => 'contacte',
    'hostname' => siteConfig()['recaptcha_hostname'],
    'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',
];
