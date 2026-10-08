<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/blog.php';
require_once __DIR__ . '/includes/contact.php';

$errors = [];

// Només POST: index.html és estàtic i el formulari és l'únic client d'aquest endpoint.
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    $status = 'method';
} else {
    $result = validateContact($_POST);
    $errors = $result['errors'];

    if (normalizeInput($_POST['website'] ?? '') !== '') {
        // Honeypot: un bot ha omplert el camp ocult. Es respon com a èxit i no s'envia res.
        $status = 'success';
    } elseif ($errors !== []) {
        http_response_code(422);
        $status = 'invalid';
    } elseif (sendContactMail(require __DIR__ . '/includes/mail-config.php', $result['data'])) {
        $status = 'success';
    } else {
        http_response_code(500);
        $status = 'error';
    }
}

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
