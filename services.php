<?php
/**
 * NOVA TECH - Services Page
 * 
 * Loads services from the MySQL database (services table),
 * with server-side search and category filtering.
 * 
 * Documents: PDO prepared statements, sanitized output,
 * controlling a dynamic page through GET parameters.
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// ============================================================
// SEARCH & CATEGORY FILTER
// ============================================================
$search = trim($_GET['q'] ?? '');
$category_filter = trim($_GET['category'] ?? '');

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(title LIKE ? OR short_description LIKE ? OR full_description LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if (!empty($category_filter)) {
    $where[] = "category = ?";
    $params[] = $category_filter;
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) . ' AND is_active = 1' : 'WHERE is_active = 1';

// Fetch services from the database
$services = fetch_all($pdo, "
    SELECT * FROM services $where_sql 
    ORDER BY sort_order ASC, id ASC
", $params);

// Decode the JSON "features" column into a PHP array
foreach ($services as &$s) {
    $s['features_list'] = json_decode($s['features'] ?? '[]', true) ?: [];
}
unset($s);

// Get all categories for the filter buttons
$categories = fetch_all($pdo, "
    SELECT DISTINCT category FROM services 
    WHERE is_active = 1 
    ORDER BY category ASC
");

$page_title = 'Services';
$page_description = 'Explore NOVA TECH services: Web Development, Mobile Apps, AI, Cyber Security, Cloud and UI/UX Design.';
include __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     PAGE HERO
============================================================ -->
<section class="page-hero">
    <div class="container">
        <h1 class="page-hero__title">Our Services</h1>
        <p class="page-hero__desc">
            From first wireframe to global launch — everything you need to build,
            secure and scale modern digital products.
        </p>
        <div class="breadcrumb">
            <a href="<?= base_url('index.php') ?>">Home</a> / <span>Services</span>
        </div>
    </div>
</section>

<!-- ============================================================
     SERVICES (from MySQL database)
============================================================ -->
<section class="services-page section-pad">
    <div class="container">

        <!-- Toolbar: search + category filter (server-side via GET) -->
        <form class="services-toolbar" method="GET" action="<?= base_url('services.php') ?>">
            <input type="text" name="q" class="services-search" placeholder="Search services, e.g. AI..." aria-label="Search services" value="<?= sanitize($search) ?>">
            <div class="filter-bar">
                <a class="filter-btn <?= $category_filter === '' ? 'active' : '' ?>" href="<?= base_url('services.php' . (!empty($search) ? '?q=' . urlencode($search) : '')) ?>">All</a>
                <?php foreach ($categories as $cat): ?>
                    <a class="filter-btn <?= $category_filter === $cat['category'] ? 'active' : '' ?>" 
                       href="<?= base_url('services.php?category=' . urlencode($cat['category']) . (!empty($search) ? '&q=' . urlencode($search) : '')) ?>">
                        <?= sanitize($cat['category']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </form>

        <!-- Result count -->
        <p class="services-count"><?= count($services) ?> service<?= count($services) === 1 ? '' : 's' ?> found</p>

        <!-- Cards rendered by PHP from the services table -->
        <div class="services-detail-grid">
            <?php if (empty($services)): ?>
                <p class="services-empty show">No services match your search. Try a different keyword.</p>
            <?php else: ?>
                <?php foreach ($services as $service): ?>
                <article class="service-card service-card--detail card-enter">
                    <div class="service-card__icon" aria-hidden="true">
                        <i class="<?= sanitize($service['icon']) ?>"></i>
                    </div>
                    <h3 class="service-card__title"><?= sanitize($service['title']) ?></h3>
                    <p class="service-card__desc"><?= sanitize($service['short_description']) ?></p>
                    <?php if (!empty($service['features_list'])): ?>
                        <ul class="service-card__features">
                            <?php foreach (array_slice($service['features_list'], 0, 5) as $feature): ?>
                                <li><?= sanitize($feature) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    <?php if ($service['price_from'] > 0): ?>
                        <p class="service-card__price">From $<?= number_format($service['price_from'], 0) ?></p>
                    <?php endif; ?>
                    <a href="<?= base_url('contact.php') ?>" class="btn btn--ghost btn--sm" style="width:100%;text-align:center;">Get Started</a>
                </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</section>

<!-- ============================================================
     CTA STRIP
============================================================ -->
<section class="cta-strip">
    <div class="container">
        <h2>Need A Custom Solution?</h2>
        <p>Tell us about your project and we'll build the right plan for you.</p>
        <a href="<?= base_url('contact.php') ?>" class="btn btn--outline">Request A Quote</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>