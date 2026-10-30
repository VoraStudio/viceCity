<?php

declare(strict_types=1);

// Textos del servidor: llegeix els mateixos diccionaris JSON que el front (assets/i18n).
const I18N_LANGUAGES = ['ca', 'es', 'en', 'fr'];
const I18N_BASE_LANGUAGE = 'ca';
const I18N_DEFAULT_LANGUAGE = 'ca';

/** Idioma enviat pel front; qualsevol valor desconegut és l'idioma per defecte. */
function i18nLanguage(mixed $value): string
{
    return is_string($value) && in_array($value, I18N_LANGUAGES, true) ? $value : I18N_DEFAULT_LANGUAGE;
}

/** Diccionari d'un idioma ja validat; si el fitxer falta o està mal format, un diccionari buit. */
function i18nDictionary(string $lang): array
{
    static $dictionaries = [];

    if (!isset($dictionaries[$lang])) {
        $json = @file_get_contents(__DIR__ . '/../assets/i18n/' . $lang . '.json');
        $data = is_string($json) ? json_decode($json, true) : null;
        $dictionaries[$lang] = is_array($data) ? $data : [];
    }

    return $dictionaries[$lang];
}

/** Valor d'una clau amb punts (contact.form.required); un valor buit compta com a absent. */
function i18nLookup(array $dictionary, string $key): ?string
{
    $node = $dictionary;
    foreach (explode('.', $key) as $part) {
        if (!is_array($node) || !array_key_exists($part, $node)) {
            return null;
        }
        $node = $node[$part];
    }

    return is_string($node) && $node !== '' ? $node : null;
}

/**
 * Text traduït: idioma demanat → català → la clau mateixa. Els {marcadors} es substitueixen per $params.
 *
 * @param array<string, string|int> $params
 */
function t(string $key, string $lang = I18N_BASE_LANGUAGE, array $params = []): string
{
    $lang = i18nLanguage($lang);
    $text = i18nLookup(i18nDictionary($lang), $key)
        ?? i18nLookup(i18nDictionary(I18N_BASE_LANGUAGE), $key)
        ?? $key;

    $replacements = [];
    foreach ($params as $name => $value) {
        $replacements['{' . $name . '}'] = (string) $value;
    }

    return strtr($text, $replacements);
}
