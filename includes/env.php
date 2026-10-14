<?php

declare(strict_types=1);

// Parser de fitxers .env: línies CLAU=VALOR, comentaris amb #, valors opcionalment entre cometes.
// No fa interpolació ni executa res: el valor és sempre text literal.
function envParse(string $contents): array
{
    $values = [];
    $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents) ?? $contents;

    foreach (preg_split('/\r\n|\n|\r/', $contents) ?: [] as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }

        if (preg_match('/^(?:export\s+)?([A-Za-z_][A-Za-z0-9_]*)\s*=(.*)$/', $line, $match) !== 1) {
            continue;
        }

        $value = envParseValue(trim($match[2]));
        if ($value === null) {
            continue;
        }

        $values[$match[1]] = $value;
    }

    return $values;
}

// Retorna el valor net, o null si la línia és invàlida (cometa sense tancar).
function envParseValue(string $raw): ?string
{
    if ($raw === '') {
        return '';
    }

    $quote = $raw[0];
    if ($quote === '"' || $quote === "'") {
        $end = strpos($raw, $quote, 1);
        if ($end === false) {
            return null;
        }

        return substr($raw, 1, $end - 1);
    }

    // Sense cometes: un # només és comentari si va precedit d'espai.
    $withoutComment = preg_replace('/\s+#.*$/', '', $raw);

    return trim($withoutComment ?? $raw);
}

// Cerca el .env a l'arrel del projecte i, si no hi és, un nivell per sobre (fora del docroot).
function envFindFile(): ?string
{
    $root = dirname(__DIR__);

    foreach ([$root . '/.env', dirname($root) . '/.env'] as $candidate) {
        if (is_file($candidate) && is_readable($candidate)) {
            return $candidate;
        }
    }

    return null;
}

// Valors del .env, carregats una sola vegada. No toca l'entorn real (putenv/$_ENV).
function envFileValues(): array
{
    static $values = null;

    if ($values !== null) {
        return $values;
    }

    $values = [];
    $file = envFindFile();
    if ($file === null) {
        return $values;
    }

    $contents = file_get_contents($file);
    if ($contents === false) {
        return $values;
    }

    $values = envParse($contents);

    return $values;
}
