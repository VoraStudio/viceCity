<?php

declare(strict_types=1);

require_once __DIR__ . '/site.php';

// Tota la configuració surt de l'entorn (o del .env de reserva). Cap valor és literal.
// Valor absent o buit = pendent: el lloc es mostra com a esborrany fins que s'ompli.
$siteEnv = static fn (string $name): string => siteEnvValue($name) ?? '';

return [
    'base_url' => $siteEnv('VICITY_BASE_URL'),
    'site_name' => $siteEnv('VICITY_SITE_NAME'),

    'mail_recipient' => $siteEnv('VICITY_MAIL_RECIPIENT'),
    'mail_sender' => $siteEnv('VICITY_MAIL_SENDER'),
    // Opcional: si falta, siteConfig() el deriva de site_name.
    'mail_subject_prefix' => $siteEnv('VICITY_MAIL_SUBJECT_PREFIX'),

    'blog_indexable' => siteParseBool($siteEnv('VICITY_BLOG_INDEXABLE')),

    'owner' => [
        'razon_social' => $siteEnv('VICITY_OWNER_RAZON_SOCIAL'),
        'nif' => $siteEnv('VICITY_OWNER_NIF'),
        'domicilio' => $siteEnv('VICITY_OWNER_DOMICILIO'),
        'registro_mercantil' => $siteEnv('VICITY_OWNER_REGISTRO_MERCANTIL'),
        'email_contacto' => $siteEnv('VICITY_OWNER_EMAIL_CONTACTO'),
        'telefono' => $siteEnv('VICITY_OWNER_TELEFONO'),
        'dpd_contacto' => $siteEnv('VICITY_OWNER_DPD_CONTACTO'),
        'autoridad_control' => $siteEnv('VICITY_OWNER_AUTORIDAD_CONTROL'),
    ],
];
