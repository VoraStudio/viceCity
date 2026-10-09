<?php

declare(strict_types=1);

function siteIsPlaceholder(string $value): bool
{
    return stripos($value, 'PENDENT') !== false;
}

function siteConfig(): array
{
    static $config = null;

    if ($config !== null) {
        return $config;
    }

    $config = require __DIR__ . '/site-config.php';

    $localFile = __DIR__ . '/site-config.local.php';
    if (is_file($localFile)) {
        $local = require $localFile;
        if (is_array($local)) {
            $config = array_replace_recursive($config, $local);
        }
    }

    $config['base_url'] = rtrim((string) $config['base_url'], '/');
    $config['recaptcha_hostname'] = (string) (parse_url($config['base_url'], PHP_URL_HOST) ?: '');

    return $config;
}

function siteUrl(string $path = ''): string
{
    return siteConfig()['base_url'] . '/' . ltrim($path, '/');
}

function sitePendingKeys(): array
{
    $pending = [];
    $walk = static function (array $values, string $prefix) use (&$walk, &$pending): void {
        foreach ($values as $key => $value) {
            $name = $prefix . $key;

            if (is_array($value)) {
                $walk($value, $name . '.');
            } elseif ($name !== 'recaptcha_hostname' && is_string($value) && siteIsPlaceholder($value)) {
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
