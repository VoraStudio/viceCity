<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/blog.php';
require_once __DIR__ . '/includes/http.php';
require_once __DIR__ . '/includes/contact.php';
require_once __DIR__ . '/includes/contact-security.php';
require_once __DIR__ . '/includes/recaptcha.php';

const CONTACT_SUCCESS_MESSAGE = "Hem rebut la teva sol·licitud. L'equip de Vicity et respondrà tan aviat com sigui possible.";

function processContact(bool $json): array
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');

        return ['code' => 405, 'state' => 'method', 'message' => 'Aquest punt només accepta el formulari de contacte.'];
    }

    if (!isSameOrigin()) {
        return ['code' => 403, 'state' => 'forbidden', 'message' => "No s'ha pogut verificar l'origen de la sol·licitud."];
    }

    startContactSession();

    if ($json && !isValidContactToken($_POST['csrf_token'] ?? null)) {
        return ['code' => 403, 'state' => 'forbidden', 'message' => 'La sessió ha caducat. Torna-ho a provar.'];
    }

    if (isContactRateLimited()) {
        header('Retry-After: ' . CONTACT_MIN_INTERVAL);

        return ['code' => 429, 'state' => 'rate', 'message' => 'Has enviat un missatge fa molt poc. Espera uns segons i torna-ho a provar.'];
    }

    if (normalizeInput($_POST['website'] ?? '') !== '') {
        return ['code' => 200, 'state' => 'success', 'message' => CONTACT_SUCCESS_MESSAGE];
    }

    if ($json && isContactSubmittedTooFast()) {
        header('Retry-After: ' . CONTACT_MIN_FILL_SECONDS);

        return ['code' => 429, 'state' => 'rate', 'message' => 'Espera un moment i torna a enviar el formulari.'];
    }

    if (recaptchaEnabled()) {
        $verdict = verifyRecaptcha(normalizeInput($_POST['recaptcha_response'] ?? ''));

        if ($verdict === 'unreachable') {
            return ['code' => 503, 'state' => 'unavailable', 'message' => "No hem pogut completar la verificació de seguretat. Torna-ho a provar d'aquí a una estona."];
        }

        if ($verdict !== 'ok') {
            return ['code' => 403, 'state' => 'forbidden', 'message' => 'No hem pogut verificar que no ets un robot. Torna-ho a provar.'];
        }
    }

    $result = validateContact($_POST);
    if ($result['errors'] !== []) {
        return [
            'code' => 422,
            'state' => 'invalid',
            'message' => 'Revisa els camps marcats i torna-ho a provar.',
            'errors' => $result['errors'],
            'fieldErrors' => $result['fieldErrors'],
        ];
    }

    if (!sendContactMail(require __DIR__ . '/includes/mail-config.php', $result['data'])) {
        return ['code' => 500, 'state' => 'error', 'message' => "No hem pogut enviar la teva sol·licitud. Torna-ho a provar d'aquí a una estona."];
    }

    markContactSubmitted();

    return ['code' => 200, 'state' => 'success', 'message' => CONTACT_SUCCESS_MESSAGE];
}

$json = wantsJson();
$outcome = processContact($json);
$status = $outcome['state'];
$errors = $outcome['errors'] ?? [];

if ($json) {
    $payload = ['ok' => $outcome['code'] < 400, 'message' => $outcome['message']];
    if ($status === 'invalid') {
        $payload['errors'] = $outcome['fieldErrors'];
        $payload['list'] = $errors;
    }
    if ($status === 'success') {
        $payload['token'] = rotateContactToken();
    }
    respondJson($outcome['code'], $payload);
}

http_response_code($outcome['code']);
header('Cache-Control: no-store');

$page = [
    'title' => 'Contacte · Vicity',
    'description' => "Resultat de l'enviament del formulari de contacte de Vicity.",
    'path' => 'contacto.php',
];

$linkClass = 'rounded-sm text-purple-700 underline focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30';

require __DIR__ . '/includes/head.php';
?>

    <main id="contingut">
      <section class="relative overflow-hidden bg-linear-to-b from-lav-100/60 to-paper pt-12 pb-16 md:pt-16 md:pb-20 lg:pt-20 lg:pb-24">
        <div aria-hidden="true" class="absolute -top-16 -right-24 size-96 rounded-full bg-lav-300/20 blur-3xl"></div>
        <div aria-hidden="true" class="absolute top-64 -left-32 size-96 rounded-full bg-lav-100/60 blur-3xl"></div>

        <div class="relative mx-auto flex w-full max-w-3xl flex-col items-start px-4 md:px-8 lg:px-10">
          <p class="inline-flex h-7 items-center rounded-full border-1 border-lav-300 bg-lav-100/60 px-3 font-body text-label font-bold tracking-wide text-purple-700 uppercase">Contacte</p>

<?php if ($status === 'success') : ?>
          <div role="status" class="mt-6 w-full rounded-3xl border-1 border-lav-100 bg-white p-6 md:p-10">
            <h1 class="font-head text-3xl leading-tight font-bold text-balance md:text-4xl">Hem rebut la teva sol·licitud</h1>
            <p class="mt-4 text-base leading-relaxed text-ink/80 md:text-lg">Gràcies pel teu interès. L'equip de Vicity et respondrà tan aviat com sigui possible.</p>
          </div>
<?php elseif ($status === 'invalid') : ?>
          <div role="alert" class="mt-6 w-full rounded-3xl border-1 border-lav-100 bg-white p-6 md:p-10">
            <h1 class="font-head text-3xl leading-tight font-bold text-balance md:text-4xl">No hem pogut enviar la sol·licitud</h1>
            <p class="mt-4 text-base leading-relaxed text-ink/80 md:text-lg">Revisa els punts següents i torna a omplir el formulari:</p>
            <ul class="mt-4 list-disc space-y-1 pl-5 text-base text-ink/80">
<?php foreach ($errors as $error) : ?>
              <li><?= e($error) ?></li>
<?php endforeach; ?>
            </ul>
          </div>
<?php elseif ($status === 'rate') : ?>
          <div role="alert" class="mt-6 w-full rounded-3xl border-1 border-lav-100 bg-white p-6 md:p-10">
            <h1 class="font-head text-3xl leading-tight font-bold text-balance md:text-4xl">Massa enviaments seguits</h1>
            <p class="mt-4 text-base leading-relaxed text-ink/80 md:text-lg"><?= e($outcome['message']) ?></p>
          </div>
<?php elseif ($status === 'forbidden' || $status === 'unavailable') : ?>
          <div role="alert" class="mt-6 w-full rounded-3xl border-1 border-lav-100 bg-white p-6 md:p-10">
            <h1 class="font-head text-3xl leading-tight font-bold text-balance md:text-4xl">No s'ha pogut verificar la sol·licitud</h1>
            <p class="mt-4 text-base leading-relaxed text-ink/80 md:text-lg"><?= e($outcome['message']) ?></p>
          </div>
<?php elseif ($status === 'error') : ?>
          <div role="alert" class="mt-6 w-full rounded-3xl border-1 border-lav-100 bg-white p-6 md:p-10">
            <h1 class="font-head text-3xl leading-tight font-bold text-balance md:text-4xl">Ha passat un error</h1>
            <p class="mt-4 text-base leading-relaxed text-ink/80 md:text-lg">
              No hem pogut enviar la teva sol·licitud. Torna-ho a provar d'aquí a una estona o escriu-nos a
              <a href="mailto:info@vicity.cat" class="<?= e($linkClass) ?>">info@vicity.cat</a>.
            </p>
          </div>
<?php else : ?>
          <div role="alert" class="mt-6 w-full rounded-3xl border-1 border-lav-100 bg-white p-6 md:p-10">
            <h1 class="font-head text-3xl leading-tight font-bold text-balance md:text-4xl">Aquesta pàgina només rep el formulari</h1>
            <p class="mt-4 text-base leading-relaxed text-ink/80 md:text-lg">Per contactar amb nosaltres, omple el formulari de contacte.</p>
          </div>
<?php endif; ?>

          <a
            href="index.html#contacte"
            class="mt-6 inline-flex h-11 items-center gap-2 rounded-full font-head text-sm font-semibold text-purple-700 hover:underline focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
            >Torna al formulari</a
          >
        </div>
      </section>
    </main>

<?php require __DIR__ . '/includes/foot.php'; ?>
