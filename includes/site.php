<?php

declare(strict_types=1);

require_once __DIR__ . '/env.php';

// Un valor és pendent si és buit o només espais.
function siteIsPlaceholder(string $value): bool
{
    return trim($value) === '';
}

// Retorna el valor d'una variable de configuració, o null si no existeix o és buida.
// Precedència: entorn real (getenv, $_SERVER, $_ENV, també amb prefix REDIRECT_ de SetEnv d'Apache) > .env.
function siteEnvValue(string $name): ?string
{
    foreach ([$name, 'REDIRECT_' . $name] as $candidate) {
        $sources = [getenv($candidate), $_SERVER[$candidate] ?? null, $_ENV[$candidate] ?? null];

        foreach ($sources as $value) {
            if (is_string($value) && trim($value) !== '') {
                return $value;
            }
        }
    }

    $fileValue = envFileValues()[$name] ?? null;

    return is_string($fileValue) && trim($fileValue) !== '' ? $fileValue : null;
}

function siteParseBool(string $value): bool
{
    return in_array(strtolower(trim($value)), ['1', 'true', 'on', 'yes'], true);
}

function siteConfig(): array
{
    static $config = null;

    if ($config !== null) {
        return $config;
    }

    $config = require __DIR__ . '/site-config.php';

    $config['base_url'] = rtrim(trim((string) $config['base_url']), '/');
    $config['recaptcha_hostname'] = (string) (parse_url($config['base_url'], PHP_URL_HOST) ?: '');

    if (siteIsPlaceholder((string) $config['mail_subject_prefix'])) {
        $siteName = trim((string) $config['site_name']);
        $config['mail_subject_prefix'] = $siteName === '' ? '' : '[' . $siteName . ']';
    }

    return $config;
}

// Nom de marca net; cadena buida si encara és pendent.
function siteName(): string
{
    return trim((string) siteConfig()['site_name']);
}

// Complement "de X" / "d'X" (amb elisió davant vocal o h); buit si no hi ha nom.
function siteNameOf(): string
{
    $name = siteName();

    if ($name === '') {
        return '';
    }

    return preg_match('/^[aeiouàèéíòóúh]/iu', $name) === 1 ? "d'" . $name : 'de ' . $name;
}

// Títol de pàgina amb el nom de marca com a sufix; sense nom queda només el títol.
function sitePageTitle(string $title): string
{
    $name = siteName();

    return $name === '' ? $title : $title . ' · ' . $name;
}

function siteHasBaseUrl(): bool
{
    return siteConfig()['base_url'] !== '';
}

// Amb base_url buit retorna una ruta relativa a l'arrel (mai una URL absoluta trencada).
function siteUrl(string $path = ''): string
{
    return siteConfig()['base_url'] . '/' . ltrim($path, '/');
}

function sitePendingKeys(): array
{
    // Camps que no compten com a pendents: derivats o opcionals.
    $ignored = ['recaptcha_hostname', 'mail_subject_prefix'];
    $pending = [];
    $walk = static function (array $values, string $prefix) use (&$walk, &$pending, $ignored): void {
        foreach ($values as $key => $value) {
            $name = $prefix . $key;

            if (is_array($value)) {
                $walk($value, $name . '.');
            } elseif (!in_array($name, $ignored, true) && is_string($value) && siteIsPlaceholder($value)) {
                $pending[] = $name;
            }
        }
    };
    $walk(siteConfig(), '');

    return $pending;
}

function siteIsConfigured(): bool
{
    return sitePendingKeys() === [];
}

function siteMailConfigured(): bool
{
    $config = siteConfig();

    return !siteIsPlaceholder((string) $config['mail_recipient']) && !siteIsPlaceholder((string) $config['mail_sender']);
}
