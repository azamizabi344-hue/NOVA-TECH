<?php
/**
 * NOVA TECH - Single Blog Post Page
 * 
 * Shows one full blog article based on the "slug" in the URL.
 * Example: post.php?slug=how-we-built-a-real-time-chat
 * 
 * - Fetches the matching post with JOINs for category and author
 * - Increments the view counter
 * - Builds a "related posts" list from the same category
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

$slug = trim($_GET['slug'] ?? '');

// If no slug provided, redirect to the blog listing
if (empty($slug)) {
    redirect(base_url('blog.php'));
}

// Fetch the post by its unique slug
$post = fetch_one($pdo, "
    SELECT bp.*, c.name as category_name, c.slug as category_slug, u.full_name as author_name
    FROM blog_posts bp 
    LEFT JOIN categories c ON bp.category_id = c.id 
    LEFT JOIN users u ON bp.author_id = u.id 
    WHERE bp.slug = ? AND bp.is_published = 1
", [$slug]);

// 404 if not found
if (!$post) {
    http_response_code(404);
    $page_title = 'Post Not Found';
    include __DIR__ . '/includes/header.php';
    echo '<section class="page-hero"><div class="container"><h1 class="page-hero__title">Post Not Found</h1><p class="page-hero__desc">The article you are looking for does not exist.</p><div class="breadcrumb"><a href="blog.php">Back to Blog</a></div></div></section>';
    include __DIR__ . '/includes/footer.php';
    exit;
}

// Increment the view counter for this post
execute_query($pdo, "UPDATE blog_posts SET views = views + 1 WHERE id = ?", [$post['id']]);
$post['views']++;

// Related posts (same category, exclude current post)
$related = fetch_all($pdo, "
    SELECT id, title, slug, image, created_at 
    FROM blog_posts 
    WHERE category_id = ? AND id != ? AND is_published = 1 
    ORDER BY created_at DESC 
    LIMIT 3
", [$post['category_id'], $post['id']]);

$page_title = $post['title'];
$page_description = $post['excerpt'] ?: truncate(strip_tags($post['content']), 150);
include __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     PAGE HERO (article header)
============================================================ -->
<section class="page-hero page-hero--article">
    <div class="container">
        <div class="breadcrumb">
            <a href="<?= base_url('index.php') ?>">Home</a> /
            <a href="<?= base_url('blog.php') ?>">Blog</a> /
            <?php if ($post['category_slug']): ?><a href="<?= base_url('blog.php?category=' . urlencode($post['category_slug'])) ?>"><?= sanitize($post['category_name']) ?></a> /<?php endif; ?>
            <span>Article</span>
        </div>
        <h1 class="page-hero__title" style="max-width:900px;margin:20px auto 0;"><?= sanitize($post['title']) ?></h1>
        <p class="page-hero__desc">
            By <?= sanitize($post['author_name'] ?? 'NOVA TECH') ?> &middot;
            <?= format_date($post['created_at'], 'M d, Y') ?> &middot;
            <?= (int)$post['views'] ?> views
        </p>
    </div>
</section>

<!-- ============================================================
     ARTICLE CONTENT
============================================================ -->
<section class="section-pad">
    <div class="container" style="max-width:820px;">
        <?php if ($post['image']): ?>
            <div class="article-hero-image" style="border-radius:var(--radius-lg);overflow:hidden;margin-bottom:28px;">
                <img src="<?= base_url('uploads/blog/' . $post['image']) ?>" alt="<?= sanitize($post['title']) ?>" style="width:100%;display:block;">
            </div>
        <?php endif; ?>

        <article class="article-body">
            <?php
            // The content column may contain trusted HTML we authored.
            // We still pass it through a sanitizer that strips dangerous tags.
            // (A pragmatic choice for a learning project: the DB is admin-controlled.)
            echo preg_replace(
                ['/<script\b[^>]*>(.*?)<\/script>/is', '/on\w+\s*=/i', '/javascript\s*:/i'],
                ['', '', ''],
                $post['content']
            );
            ?>
        </article>

        <!-- Share / actions -->
        <div style="display:flex;gap:12px;margin-top:32px;flex-wrap:wrap;">
            <a href="<?= base_url('blog.php') ?>" class="btn btn--ghost"><i class="fas fa-arrow-left"></i> Back to Blog</a>
            <a href="<?= base_url('contact.php') ?>" class="btn btn--primary"><i class="fas fa-comment"></i> Ask Us About This</a>
        </div>

        <!-- Related posts -->
        <?php if (!empty($related)): ?>
            <div style="margin-top:48px;">
                <h2 class="services-count" style="margin-bottom:18px;">Related Articles</h2>
                <div class="blog__grid">
                    <?php foreach ($related as $rel): ?>
                        <article class="blog-card card-enter">
                            <a href="<?= base_url('post.php?slug=' . urlencode($rel['slug'])) ?>" class="blog-card__image">NT<span class="blog-card__category"><?= sanitize($post['category_name'] ?? '') ?></span></a>
                            <div class="blog-card__body">
                                <h3 class="blog-card__title"><a href="<?= base_url('post.php?slug=' . urlencode($rel['slug'])) ?>"><?= sanitize($rel['title']) ?></a></h3>
                                <div class="blog-card__meta"><span><?= format_date($rel['created_at']) ?></span></div>
                                <a class="blog-card__btn" href="<?= base_url('post.php?slug=' . urlencode($rel['slug'])) ?>">Read More &rarr;</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>