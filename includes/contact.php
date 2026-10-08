<?php

declare(strict_types=1);

// Camps de text d'una sola línia: mai poden dur salts de línia (injecció de capçaleres).
const CONTACT_SINGLE_LINE_LIMITS = [
    'nom' => 100,
    'organitzacio' => 150,
    'carrec' => 100,
    'email' => 254,
    'telefon' => 30,
];

const CONTACT_MESSAGE_LIMIT = 1000;

const CONTACT_REASONS = [
    'demo' => 'Sol·licitud de demostració',
    'especialista' => 'Parlar amb un especialista',
    'altres' => 'Altres consultes',
];

const CONTACT_FIELD_LABELS = [
    'nom' => 'Nom i cognoms',
    'organitzacio' => 'Organització',
    'carrec' => 'Càrrec',
    'email' => 'Correu electrònic',
    'telefon' => 'Telèfon',
];

/** Retalla un valor de POST; qualsevol cosa que no sigui text (p. ex. arrays) es converteix en cadena buida. */
function normalizeInput(mixed $value): string
{
    return is_string($value) ? trim($value) : '';
}

/** Indica si el text conté salts de línia o bytes nuls (vector d'injecció de capçaleres). */
function hasLineBreaks(string $value): bool
{
    return preg_match('/[\r\n\x00]/', $value) === 1;
}

/**
 * Valida les dades del formulari.
 *
 * @param array<string, mixed> $input Normalment $_POST.
 * @return array{data: array<string, string>, errors: list<string>}
 */
function validateContact(array $input): array
{
    $data = [];
    $errors = [];

    foreach (CONTACT_SINGLE_LINE_LIMITS as $field => $limit) {
        $value = normalizeInput($input[$field] ?? '');
        $data[$field] = $value;
        $label = CONTACT_FIELD_LABELS[$field];

        if (hasLineBreaks($value)) {
            $errors[] = "El camp «{$label}» conté caràcters no permesos.";
        } elseif (mb_strlen($value) > $limit) {
            $errors[] = "El camp «{$label}» no pot superar els {$limit} caràcters.";
        }
    }

    foreach (['nom', 'organitzacio', 'email'] as $requiredField) {
        if ($data[$requiredField] === '') {
            $errors[] = 'El camp «' . CONTACT_FIELD_LABELS[$requiredField] . '» és obligatori.';
        }
    }

    $email = $data['email'];
    if ($email !== '' && !hasLineBreaks($email) && filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        $errors[] = 'Introdueix un correu electrònic vàlid.';
    }

    if ($data['telefon'] !== '' && preg_match('/^\+?[0-9 ().-]{6,30}$/', $data['telefon']) !== 1) {
        $errors[] = 'Introdueix un telèfon vàlid.';
    }

    $data['motiu'] = normalizeInput($input['motiu'] ?? '');
    if (!array_key_exists($data['motiu'], CONTACT_REASONS)) {
        $errors[] = 'Selecciona una opció a «Què necessites?».';
    }

    // El missatge és l'únic camp multilínia i només va al cos del correu, mai a les capçaleres.
    $data['missatge'] = normalizeInput($input['missatge'] ?? '');
    if (mb_strlen($data['missatge']) > CONTACT_MESSAGE_LIMIT) {
        $errors[] = 'El missatge no pot superar els ' . CONTACT_MESSAGE_LIMIT . ' caràcters.';
    }

    if (normalizeInput($input['privacitat'] ?? '') !== 'si') {
        $errors[] = 'Cal acceptar la política de privacitat.';
    }

    return ['data' => $data, 'errors' => $errors];
}

/**
 * Assumpte ja codificat (RFC 2047). Només usa el prefix de la configuració i
 * l'etiqueta d'un motiu de la llista tancada, mai text de l'usuari.
 *
 * @param array<string, string> $config
 */
function buildMailSubject(array $config, string $reasonKey): string
{
    $subject = $config['subject_prefix'] . ' ' . CONTACT_REASONS[$reasonKey];

    return mb_encode_mimeheader($subject, 'UTF-8', 'B', "\r\n");
}

/**
 * Cos del correu en text pla.
 *
 * @param array<string, string> $data Dades ja validades.
 */
function buildMailBody(array $data): string
{
    $lines = [
        'Nova sol·licitud des del formulari de contacte de la web.',
        '',
        'Motiu: ' . CONTACT_REASONS[$data['motiu']],
        'Nom i cognoms: ' . $data['nom'],
        'Organització: ' . $data['organitzacio'],
        'Càrrec: ' . ($data['carrec'] !== '' ? $data['carrec'] : '-'),
        'Correu electrònic: ' . $data['email'],
        'Telèfon: ' . ($data['telefon'] !== '' ? $data['telefon'] : '-'),
        '',
        'Missatge:',
        $data['missatge'] !== '' ? $data['missatge'] : '-',
    ];

    return implode("\n", $lines) . "\n";
}

/**
 * Capçaleres del correu. Reply-To és l'adreça de qui escriu (ja validada, sense salts de línia).
 *
 * @param array<string, string> $config
 * @param array<string, string> $data Dades ja validades.
 * @return array<string, string>
 */
function buildMailHeaders(array $config, array $data): array
{
    return [
        'From' => $config['sender'],
        'Reply-To' => $data['email'],
        'MIME-Version' => '1.0',
        'Content-Type' => 'text/plain; charset=UTF-8',
        'Content-Transfer-Encoding' => '8bit',
    ];
}

/**
 * Envia la sol·licitud amb mail(). Retorna false si el sistema de correu la rebutja.
 *
 * @param array<string, string> $config
 * @param array<string, string> $data Dades ja validades.
 */
function sendContactMail(array $config, array $data): bool
{
    return mail(
        $config['recipient'],
        buildMailSubject($config, $data['motiu']),
        buildMailBody($data),
        buildMailHeaders($config, $data),
    );
}
