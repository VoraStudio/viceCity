<?php

declare(strict_types=1);

// Configuració del correu del formulari de contacte.
// IMPORTANT: ajusteu aquests valors a l'allotjament. El remitent hauria de ser una
// adreça del mateix domini que el servidor (i amb SPF/DKIM configurats), si no, molts
// proveïdors marcaran els correus com a brossa.
return [
    'recipient' => 'info@vicity.cat',
    'sender' => 'noreply@vicity.cat',
    'subject_prefix' => '[Vicity web]',
];
