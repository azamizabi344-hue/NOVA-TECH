<?php
/**
 * NOVA TECH - Blog Page
 * 
 * Loads blog posts from the MySQL database (blog_posts + categories tables).
 * Features:
 * - Search by title/excerpt/content
 * - Filter by category
 * - Pagination (server-side, using LIMIT / OFFSET)
 * 
 * PAGINATION EXPLAINED:
 * SQL uses "LIMIT X OFFSET Y" to fetch only one page of rows.
 *   page 1 -> LIMIT 6 OFFSET 0   (rows 1-6)
 *   page 2 -> LIMIT 6 OFFSET 6   (rows 7-12)
 * We compute OFFSET in PHP: ($page - 1) * $per_page
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// ============================================================
// SEARCH / CATEGORY / PAGINATION
// ============================================================
$search = trim($_GET['q'] ?? '');
$cat_filter = trim($_GET['category'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$per_page = 6;

// Build WHERE clause
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(bp.title LIKE ? OR bp.excerpt LIKE ? OR bp.content LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if (!empty($cat_filter)) {
    $where[] = "c.slug = ?";
    $params[] = $cat_filter;
}

// Only published posts appear publicly
$where[] = "bp.is_published = 1";
$where_sql = 'WHERE ' . implode(' AND ', $where);

// Count total matching posts (for pagination)
$total = count_rows($pdo, "
    SELECT COUNT(*) as total 
    FROM blog_posts bp 
    LEFT JOIN categories c ON bp.category_id = c.id 
    $where_sql
", $params);

$total_pages = max(1, (int)ceil($total / $per_page));
$page = min($page, $total_pages);
$offset = ($page - 1) * $per_page;

// Fetch the current page of posts
$posts = fetch_all($pdo, "
    SELECT bp.*, c.name as category_name, c.slug as category_slug, u.full_name as author_name
    FROM blog_posts bp 
    LEFT JOIN categories c ON bp.category_id = c.id 
    LEFT JOIN users u ON bp.author_id = u.id 
    $where_sql 
    ORDER BY bp.created_at DESC 
    LIMIT $per_page OFFSET $offset
", $params);

// All categories (with counts) for the filter bar
$categories = fetch_all($pdo, "
    SELECT c.*, COUNT(bp.id) as post_count 
    FROM categories c 
    LEFT JOIN blog_posts bp ON bp.category_id = c.id AND bp.is_published = 1 
    GROUP BY c.id 
    ORDER BY c.name ASC
");

$page_title = 'Blog';
$page_description = 'Ideas, lessons and behind-the-scenes stories from our engineering, design, AI and security teams.';
include __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     PAGE HERO
============================================================ -->
<section class="page-hero">
    <div class="container">
        <h1 class="page-hero__title">Blog &amp; Insights</h1>
        <p class="page-hero__desc">
            Ideas, lessons and behind-the-scenes stories from our engineering,
            design, AI and security teams.
        </p>
        <div class="breadcrumb">
            <a href="<?= base_url('index.php') ?>">Home</a> / <span>Blog</span>
        </div>
    </div>
</section>

<!-- ============================================================
     BLOG (from MySQL database)
============================================================ -->
<section class="blog-page section-pad">
    <div class="container">

        <!-- Toolbar: search -->
        <form class="toolbar" method="GET" action="<?= base_url('blog.php') ?>">
            <input type="text" name="q" class="toolbar-search" placeholder="Search articles, topics or authors..." aria-label="Search articles" value="<?= sanitize($search) ?>">
        </form>

        <!-- Category filter -->
        <div class="filter-bar">
            <a class="filter-btn <?= $cat_filter === '' ? 'active' : '' ?>" href="<?= base_url('blog.php' . (!empty($search) ? '?q=' . urlencode($search) : '')) ?>">All</a>
            <?php foreach ($categories as $cat): ?>
                <a class="filter-btn <?= $cat_filter === $cat['slug'] ? 'active' : '' ?>"
                   href="<?= base_url('blog.php?category=' . urlencode($cat['slug']) . (!empty($search) ? '&q=' . urlencode($search) : '')) ?>">
                    <?= sanitize($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Result count -->
        <p class="services-count"><?= (int)$total ?> article<?= $total === 1 ? '' : 's' ?> found</p>

        <!-- Cards rendered by PHP from the blog_posts table -->
        <div class="blog__grid">
            <?php if (empty($posts)): ?>
                <p class="services-empty show">No articles match your search. Try a different keyword or category.</p>
            <?php else: ?>
                <?php foreach ($posts as $post): ?>
                    <article class="blog-card card-enter">
                        <a href="<?= base_url('post.php?slug=' . urlencode($post['slug'])) ?>" class="blog-card__image" <?= $post['image'] ? 'style="background-image:url(\'' . base_url('uploads/blog/' . $post['image']) . '\');background-size:cover;background-position:center;font-size:0;"' : '' ?>>
                            <?= !$post['image'] ? 'NT' : '' ?>
                            <span class="blog-card__category"><?= sanitize($post['category_name'] ?? 'General') ?></span>
                        </a>
                        <div class="blog-card__body">
                            <h3 class="blog-card__title">
                                <a href="<?= base_url('post.php?slug=' . urlencode($post['slug'])) ?>"><?= sanitize($post['title']) ?></a>
                            </h3>
                            <div class="blog-card__meta">
                                <span class="blog-card__author">
                                    <span class="blog-card__avatar"><?= sanitize(strtoupper(substr($post['author_name'] ?? 'N', 0, 1))) ?></span>
                                    <?= sanitize($post['author_name'] ?? 'NOVA TECH') ?>
                                </span>
                                <span><?= format_date($post['created_at']) ?></span>
                                <span><?= (int)$post['views'] ?> views</span>
                            </div>
                            <p class="blog-card__desc"><?= sanitize(truncate($post['excerpt'] ?: strip_tags($post['content']), 120)) ?></p>
                            <a class="blog-card__btn" href="<?= base_url('post.php?slug=' . urlencode($post['slug'])) ?>">Read More &rarr;</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Pagination links -->
        <?php if ($total_pages > 1): ?>
            <nav class="pagination" aria-label="Blog pagination">
                <?php if ($page > 1): ?>
                    <a href="<?= base_url('blog.php?' . http_build_query(array_merge(array_filter(['q' => $search, 'category' => $cat_filter]), ['page' => $page - 1]))) ?>">&laquo; Prev</a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i === $page): ?>
                        <span class="pagination__current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= base_url('blog.php?' . http_build_query(array_merge(array_filter(['q' => $search, 'category' => $cat_filter]), ['page' => $i]))) ?>"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="<?= base_url('blog.php?' . http_build_query(array_merge(array_filter(['q' => $search, 'category' => $cat_filter]), ['page' => $page + 1]))) ?>">Next &raquo;</a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>

    </div>
</section>

<!-- ============================================================
     CTA STRIP
============================================================ -->
<section class="cta-strip">
    <div class="container">
        <h2>Want To Work With Us?</h2>
        <p>Explore our open roles or start a conversation.</p>
        <a href="<?= base_url('contact.php') ?>" class="btn btn--outline">Contact Us</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>