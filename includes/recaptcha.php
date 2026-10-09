<?php

declare(strict_types=1);

function recaptchaConfig(): array
{
    static $config = null;

    return $config ??= require __DIR__ . '/recaptcha-config.php';
}

function isRecaptchaPlaceholder(string $value): bool
{
    return $value === '' || str_starts_with($value, 'PENDENT');
}

function recaptchaEnabled(): bool
{
    $config = recaptchaConfig();

    return !isRecaptchaPlaceholder((string) $config['site_key']) && !isRecaptchaPlaceholder((string) $config['secret_key']);
}

function recaptchaSiteKey(): ?string
{
    return recaptchaEnabled() ? (string) recaptchaConfig()['site_key'] : null;
}

function requestSiteverify(string $url, string $secret, string $response): ?array
{
    if (!function_exists('curl_init')) {
        return null;
    }

    $handle = curl_init($url);
    curl_setopt_array($handle, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query(['secret' => $secret, 'response' => $response]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
    ]);

    $body = curl_exec($handle);
    $status = curl_getinfo($handle, CURLINFO_RESPONSE_CODE);

    if (!is_string($body) || $status !== 200) {
        return null;
    }

    $decoded = json_decode($body, true);

    return is_array($decoded) ? $decoded : null;
}

function verifyRecaptcha(string $response): string
{
    if ($response === '' || strlen($response) > 4096) {
        return 'rejected';
    }

    $config = recaptchaConfig();
    $result = requestSiteverify((string) $config['verify_url'], (string) $config['secret_key'], $response);

    if ($result === null) {
        return 'unreachable';
    }

    $valid = ($result['success'] ?? false) === true
        && ($result['action'] ?? null) === $config['action']
        && ($result['hostname'] ?? null) === $config['hostname']
        && is_numeric($result['score'] ?? null)
        && (float) $result['score'] >= (float) $config['min_score'];

    return $valid ? 'ok' : 'rejected';
}
