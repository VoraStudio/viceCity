<?php

declare(strict_types=1);

return [
    // PENDENT: domini definitiu contractat pel client, amb https i sense barra final.
    'base_url' => 'https://PENDENT-DOMINI.tld',
    'site_name' => 'Vicity',

    // PENDENT: adreces del domini contractat; el remitent ha de tenir SPF i DKIM configurats.
    'mail_recipient' => 'PENDENT: correu de destí del formulari',
    'mail_sender' => 'PENDENT: correu remitent del formulari',
    'mail_subject_prefix' => '[Vicity web]',

    'blog_indexable' => false,

    // PENDENT: dades del titular, a facilitar pel client.
    'owner' => [
        'razon_social' => 'PENDENT: raó social',
        'nif' => 'PENDENT: NIF/CIF',
        'domicilio' => 'PENDENT: adreça completa',
        'registro_mercantil' => 'PENDENT: registre mercantil, tom, foli i full',
        'email_contacto' => 'PENDENT: correu de contacte',
        'telefono' => 'PENDENT: telèfon',
        'dpd_contacto' => 'PENDENT: contacte del DPD',
        'autoridad_control' => 'PENDENT: autoritat de control (AEPD o Autoritat Catalana de Protecció de Dades)',
    ],
];
