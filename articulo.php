<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/blog.php';

$slug = $_GET['slug'] ?? null;
$post = is_string($slug) ? getPostBySlug(getDatabase(), $slug) : null;

if ($post === null) {
    http_response_code(404);
    $page = [
        'title' => 'Article no trobat · Vicity',
        'description' => "L'article que busques no existeix o s'ha retirat.",
        'path' => 'blog.php',
    ];
} else {
    $page = [
        'title' => $post['title'] . ' · Vicity',
        'description' => $post['excerpt'],
        'path' => getArticleUrl($post['slug']),
    ];
    // Cos en text pla: els paràgrafs se separen per una o més línies en blanc.
    $paragraphs = preg_split('/\R{2,}/', trim($post['body']));
}

$backLinkClass = 'inline-flex items-center gap-2 rounded-full font-head text-sm font-semibold text-purple-700 hover:underline focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30';

require __DIR__ . '/includes/head.php';
?>

    <main id="contingut">
<?php if ($post === null) : ?>
      <section class="bg-linear-to-b from-lav-100/60 to-paper pt-12 pb-16 md:pt-16 md:pb-20 lg:pt-20 lg:pb-24" aria-labelledby="article-titol">
        <div class="mx-auto w-full max-w-3xl px-4 md:px-8">
          <h1 id="article-titol" class="font-head text-3xl leading-tight font-bold text-balance md:text-5xl">Article no trobat</h1>
          <p class="mt-5 text-base leading-relaxed text-ink/80 md:text-lg">L'article que busques no existeix o s'ha retirat.</p>
          <a href="blog.php" class="mt-6 <?= e($backLinkClass) ?>">Torna al blog</a>
        </div>
      </section>
<?php else : ?>
      <section class="bg-linear-to-b from-lav-100/60 to-paper pt-12 pb-16 md:pt-16 md:pb-20 lg:pt-20 lg:pb-24" aria-labelledby="article-titol">
        <div class="mx-auto w-full max-w-3xl px-4 md:px-8">
          <a href="blog.php" class="<?= e($backLinkClass) ?>">Torna al blog</a>

          <article class="mt-8">
            <header>
              <p class="inline-flex h-7 items-center rounded-full border-1 border-lav-300 bg-lav-100/60 px-3 font-body text-label font-bold tracking-wide text-purple-700 uppercase"><?= e(BLOG_CATEGORIES[$post['category']]) ?></p>
              <h1 id="article-titol" class="mt-5 font-head text-3xl leading-tight font-bold text-balance md:text-5xl"><?= e($post['title']) ?></h1>
              <p class="mt-4 text-label font-medium text-ink/70">
                <time datetime="<?= e($post['published_at']) ?>"><?= e(formatDateCatalan($post['published_at'])) ?></time> · <?= e($post['read_minutes']) ?> min de lectura
              </p>
            </header>

            <div class="mt-8 flex flex-col gap-5 text-base leading-relaxed text-ink/80 md:text-lg">
<?php foreach ($paragraphs as $paragraph) : ?>
              <p><?= e($paragraph) ?></p>
<?php endforeach; ?>
            </div>
          </article>
        </div>
      </section>
<?php endif; ?>
    </main>

<?php require __DIR__ . '/includes/foot.php'; ?>
