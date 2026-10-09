<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/blog.php';

// Categoria desconeguda a ?categoria=: es mostren totes les entrades (no es dona 404,
// perquè un enllaç antic o mal escrit continuï portant a una pàgina útil).
$activeCategory = getRequestedCategory();

$posts = getPosts(getDatabase(), $activeCategory);

// Destacada: la primera is_featured de la llista (ja ordenada de més a menys recent).
$featuredPost = null;
foreach ($posts as $post) {
    if ($post['is_featured']) {
        $featuredPost = $post;
        break;
    }
}
$cardPosts = array_values(array_filter($posts, static fn (array $post): bool => $post !== $featuredPost));

$chipBaseClass = 'inline-flex h-10 items-center rounded-full px-4 font-head text-sm font-semibold whitespace-nowrap transition-colors motion-reduce:transition-none focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30';
$chipActiveClass = 'bg-purple-700 text-white';
$chipInactiveClass = 'text-ink hover:text-purple-700';

$page = [
    'title' => 'Blog · Vicity',
    'description' => 'El blog de Vicity: articles sobre gestió tributària i transformació digital de les administracions públiques.',
    'path' => 'blog.php',
];

require __DIR__ . '/includes/head.php';
?>

    <main id="contingut" class="bg-linear-to-b from-paper to-lav-100/40">
      <!-- ===================  CAPÇALERA DEL BLOG  =================== -->
      <section class="relative overflow-x-clip bg-linear-to-b from-lav-100/60 to-transparent pt-12 pb-10 md:pt-16 md:pb-12 lg:pt-20" aria-labelledby="blog-titol">
        <div aria-hidden="true" class="absolute -top-16 -right-24 size-96 rounded-full bg-lav-300/20 blur-3xl"></div>
        <div aria-hidden="true" class="absolute top-64 -left-32 size-96 rounded-full bg-lav-100/60 blur-3xl"></div>

        <div data-blog-hero class="relative mx-auto flex w-full max-w-7xl flex-col items-start px-4 md:items-center md:px-8 lg:px-10">
          <p class="inline-flex h-7 items-center rounded-full border-1 border-lav-300 bg-lav-100/60 px-3 font-body text-label font-bold tracking-wide text-purple-700 uppercase">Blog</p>
          <h1 id="blog-titol" class="mt-6 max-w-4xl font-head text-3xl leading-tight font-bold text-balance md:text-center md:text-5xl lg:text-title-1">
            Idees i recursos per a la gestió tributària municipal
          </h1>
          <p class="mt-5 max-w-4xl text-base leading-relaxed text-ink/80 md:text-center md:text-lg lg:mt-6">
            Articles pràctics sobre recaptació, control i seguiment, integració i intel·ligència artificial per a ajuntaments, diputacions i consells
            comarcals.
          </p>

          <nav aria-label="Categories d'articles" class="mt-8 max-w-full md:mt-10">
            <ul class="flex items-center gap-1 overflow-x-auto rounded-full border-1 border-lav-100 bg-white p-1">
              <li>
                <a
                  href="blog.php"
                  <?= $activeCategory === null ? 'aria-current="page"' : '' ?>
                  class="<?= e($chipBaseClass . ' ' . ($activeCategory === null ? $chipActiveClass : $chipInactiveClass)) ?>"
                  >Tots</a
                >
              </li>
<?php foreach (BLOG_CATEGORIES as $categorySlug => $categoryLabel) : ?>
<?php $isActive = $categorySlug === $activeCategory; ?>
              <li>
                <a
                  href="blog.php?categoria=<?= e(rawurlencode($categorySlug)) ?>"
                  <?= $isActive ? 'aria-current="page"' : '' ?>
                  class="<?= e($chipBaseClass . ' ' . ($isActive ? $chipActiveClass : $chipInactiveClass)) ?>"
                  ><?= e($categoryLabel) ?></a
                >
              </li>
<?php endforeach; ?>
            </ul>
          </nav>
        </div>
      </section>

<?php if ($posts === []) : ?>
      <!-- ===================  SENSE ARTICLES  =================== -->
      <section class="pb-16 md:pb-20 lg:pb-24" aria-labelledby="buit-titol">
        <div class="mx-auto w-full max-w-7xl px-4 md:px-8 lg:px-10">
          <div class="rounded-3xl border-1 border-lav-100 bg-white p-6 text-center md:p-10">
            <h2 id="buit-titol" class="font-head text-xl leading-tight font-bold md:text-2xl">Encara no hi ha articles en aquesta categoria</h2>
            <p class="mt-3 text-base leading-relaxed text-ink/70">Torna més endavant o consulta tots els articles publicats.</p>
            <a
              href="blog.php"
              class="mt-5 inline-flex items-center gap-2 rounded-full font-head text-sm font-semibold text-purple-700 hover:underline focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
              >Veure tots els articles</a
            >
          </div>
        </div>
      </section>
<?php endif; ?>

<?php if ($featuredPost !== null) : ?>
      <!-- ===================  ARTICLE DESTACAT  =================== -->
      <section class="pb-12 md:pb-14 lg:pb-16" aria-labelledby="destacat-titol">
        <div class="mx-auto w-full max-w-7xl px-4 md:px-8 lg:px-10">
          <article data-blog-featured data-hover-border class="animated-border relative [--border-angle:0deg] [--border-inset:0px] grid overflow-hidden rounded-3xl border-1 border-lav-100 bg-white lg:grid-cols-2">
            <div aria-hidden="true" class="relative aspect-2/1 overflow-hidden bg-white lg:aspect-auto lg:min-h-96">
              <span class="absolute top-8 left-10 hidden font-body text-label font-bold tracking-wide text-purple-700 uppercase lg:block">Destacat</span>
              <span class="absolute -right-16 -bottom-16 hidden size-96 rounded-full bg-lav-100/60 blur-3xl lg:block"></span>
              <span class="absolute bottom-16 left-10 hidden font-head text-6xl font-bold text-lav-100 lg:block">vicity</span>
              <span class="absolute bottom-8 left-10 hidden font-body text-sm text-lav-300 lg:block">Innovació en fiscalitat local</span>
            </div>
            <div class="flex flex-col items-start gap-4 p-5 md:p-8 lg:justify-center lg:p-10">
              <p class="text-label font-medium text-ink/70">
                Destacat · <time datetime="<?= e($featuredPost['published_at']) ?>"><?= e(formatDateCatalan($featuredPost['published_at'])) ?></time> · <?= e($featuredPost['read_minutes']) ?> min
              </p>
              <h2 id="destacat-titol" class="font-head text-xl leading-snug font-bold text-balance md:text-2xl lg:text-3xl">
                <?= e($featuredPost['title']) ?>

              </h2>
              <p class="text-base leading-relaxed text-ink/70">
                <?= e($featuredPost['excerpt']) ?>

              </p>
              <a
                href="<?= e(getArticleUrl($featuredPost['slug'])) ?>"
                data-ripple
                data-ripple-text-color="var(--color-purple-700)"
                class="relative mt-2 inline-flex h-11 w-full items-center justify-center overflow-hidden rounded-full px-6 font-body focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30 bg-purple-700 text-white outline-2 -outline-offset-1 outline-purple-700 font-bold md:w-auto"
              >
                <span data-ripple-fill aria-hidden="true" class="pointer-events-none absolute top-0 left-0 size-2.5 rounded-full bg-purple-100"></span>
                <span data-ripple-text class="relative z-10">Llegir l'article</span>
                <span class="sr-only">: <?= e($featuredPost['title']) ?></span>
              </a>
            </div>
          </article>
        </div>
      </section>
<?php endif; ?>

<?php if ($cardPosts !== []) : ?>
      <!-- ===================  ÚLTIMS ARTICLES  =================== -->
      <section class="pb-16 md:pb-20 lg:pb-24" aria-labelledby="ultims-titol">
        <div class="mx-auto w-full max-w-7xl px-4 md:px-8 lg:px-10">
          <h2 id="ultims-titol" class="font-head text-xl leading-tight font-bold md:text-2xl">Últims articles publicats</h2>
          <ul class="mt-6 grid gap-4 md:grid-cols-2 md:gap-6 lg:grid-cols-3">
<?php foreach ($cardPosts as $post) : ?>
<?php $cover = COVER_STYLES[$post['cover']]; ?>
            <li class="flex">
              <article data-blog-card data-hover-border class="animated-border relative [--border-angle:0deg] [--border-inset:0px] flex w-full flex-col overflow-hidden rounded-2xl border-1 border-lav-100 bg-white">
                <div aria-hidden="true" class="relative aspect-2/1 overflow-hidden <?= e($cover['background']) ?>">
                  <span class="absolute top-4 left-4 inline-flex h-6 items-center rounded-md px-2.5 font-body text-label font-semibold <?= e($cover['badge']) ?>"><?= e(BLOG_CATEGORIES[$post['category']]) ?></span>
                  <span class="absolute -right-8 -bottom-8 size-24 rounded-full <?= e($cover['circle']) ?>"></span>
                </div>
                <div class="flex flex-1 flex-col gap-3 p-4 md:p-5">
                  <p class="text-label font-medium text-ink/70">
                    <time datetime="<?= e($post['published_at']) ?>"><?= e(formatDateCatalan($post['published_at'])) ?></time> · <?= e($post['read_minutes']) ?> min
                  </p>
                  <h3 class="font-head text-lg leading-snug font-bold text-balance lg:text-xl"><?= e($post['title']) ?></h3>
                  <p class="text-sm leading-relaxed text-ink/70"><?= e($post['excerpt']) ?></p>
                  <a
                    href="<?= e(getArticleUrl($post['slug'])) ?>"
                    class="mt-auto inline-flex items-center gap-2 self-start rounded-full pt-2 font-head text-sm font-semibold text-purple-700 hover:underline focus-visible:outline-2 focus-visible:outline-purple-500 focus-visible:ring-4 focus-visible:ring-purple-500/30"
                  >
                    Llegir l'article<span class="sr-only">: <?= e($post['title']) ?></span>
                    <svg aria-hidden="true" viewBox="0 0 16 16" class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8h10M9 4l4 4-4 4" /></svg>
                  </a>
                </div>
              </article>
            </li>
<?php endforeach; ?>
          </ul>
        </div>
      </section>
<?php endif; ?>
    </main>

<?php require __DIR__ . '/includes/foot.php'; ?>
