<?php

declare(strict_types=1);

// Únic lloc on es defineixen les categories del blog: slug (a la URL) => etiqueta visible.
// Els posts de data/posts.php es validen contra aquesta llista.
const BLOG_CATEGORIES = [
    'ia' => 'IA',
    'recaptacio' => 'Recaptació',
    'integracio' => 'Integració',
    'casos-us' => "Casos d'ús",
];
