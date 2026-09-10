<?php
/**
 * NOVA TECH - Projects Page
 * 
 * Loads projects from the MySQL database (projects table) instead of
 * hard-coded JavaScript arrays. Supports:
 * - Search by title/description/client/technologies
 * - Filter by category
 * - Sort by newest/oldest/name
 * All filtering happens server-side with PDO prepared statements.
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// ============================================================
// SEARCH / FILTER / SORT
// ============================================================
$search = trim($_GET['q'] ?? '');
$category_filter = trim($_GET['category'] ?? '');
$sort = $_GET['sort'] ?? 'newest';

// Build WHERE clause with parameters (never concatenate user input!)
$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(title LIKE ? OR description LIKE ? OR client LIKE ? OR technologies LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if (!empty($category_filter)) {
    $where[] = "category = ?";
    $params[] = $category_filter;
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) . ' AND is_active = 1' : 'WHERE is_active = 1';

// ORDER BY is built from a fixed whitelist (safe - no user input reaches SQL directly)
switch ($sort) {
    case 'oldest':
        $order_sql = "created_at ASC";
        break;
    case 'az':
        $order_sql = "title ASC";
        break;
    default:
        $order_sql = "created_at DESC";
        break;
}

$projects = fetch_all($pdo, "
    SELECT * FROM projects $where_sql 
    ORDER BY $order_sql
", $params);

// Decode JSON technologies
foreach ($projects as &$p) {
    $p['tech_array'] = json_decode($p['technologies'] ?? '[]', true) ?: [];
}
unset($p);

// Categories for the filter bar
$categories = fetch_all($pdo, "
    SELECT DISTINCT category FROM projects 
    WHERE is_active = 1 
    ORDER BY category ASC
");

$page_title = 'Projects';
$page_description = 'Real products we have designed, built and shipped for clients across web, mobile, AI and cyber security.';
include __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     PAGE HERO
============================================================ -->
<section class="page-hero">
    <div class="container">
        <h1 class="page-hero__title">Our Projects</h1>
        <p class="page-hero__desc">
            Real products we have designed, built and shipped for clients across
            web, mobile, AI and cyber security.
        </p>
        <div class="breadcrumb">
            <a href="<?= base_url('index.php') ?>">Home</a> / <span>Projects</span>
        </div>
    </div>
</section>

<!-- ============================================================
     PROJECTS (from MySQL database)
============================================================ -->
<section class="projects-page section-pad">
    <div class="container">

        <!-- Toolbar: search + sort -->
        <form class="toolbar" method="GET" action="<?= base_url('projects.php') ?>">
            <input type="text" name="q" class="toolbar-search" placeholder="Search by name, client or technology..." aria-label="Search projects" value="<?= sanitize($search) ?>">
            <div class="toolbar__right">
                <label class="toolbar__label" for="project-sort">Sort by</label>
                <select class="sort-select" name="sort" id="project-sort" onchange="this.form.submit()">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest First</option>
                    <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
                    <option value="az" <?= $sort === 'az' ? 'selected' : '' ?>>Name (A-Z)</option>
                </select>
            </div>
        </form>

        <!-- Category filter -->
        <div class="filter-bar">
            <a class="filter-btn <?= $category_filter === '' ? 'active' : '' ?>" href="<?= base_url('projects.php' . (!empty($search) ? '?q=' . urlencode($search) . '&sort=' . $sort : '?sort=' . $sort)) ?>">All</a>
            <?php foreach ($categories as $cat): ?>
                <a class="filter-btn <?= $category_filter === $cat['category'] ? 'active' : '' ?>"
                   href="<?= base_url('projects.php?category=' . urlencode($cat['category']) . '&sort=' . $sort . (!empty($search) ? '&q=' . urlencode($search) : '')) ?>">
                    <?= sanitize($cat['category']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Result count -->
        <p class="services-count"><?= count($projects) ?> project<?= count($projects) === 1 ? '' : 's' ?> found</p>

        <!-- Cards rendered by PHP from the projects table -->
        <div class="projects__grid">
            <?php if (empty($projects)): ?>
                <p class="services-empty show">No projects match your search. Try a different keyword or filter.</p>
            <?php else: ?>
                <?php foreach ($projects as $project): ?>
                    <?php
                    // Extract an image label / placeholder text from the title
                    $initials = substr($project['title'], 0, 3);
                    $image_url = $project['image'] ? base_url('uploads/projects/' . $project['image']) : null;
                    ?>
                    <article class="project-card card-enter">
                        <div class="project-card__image" <?= $image_url ? 'style="background-image:url(\'' . $image_url . '\');background-size:cover;background-position:center;"' : '' ?>>
                            <?php if (!$image_url): ?>
                                <span class="project-card__label"><?= sanitize(strtoupper($initials)) ?></span>
                            <?php endif; ?>
                            <span class="project-card__category"><?= sanitize($project['category']) ?></span>
                        </div>
                        <div class="project-card__body">
                            <h3 class="project-card__title"><?= sanitize($project['title']) ?></h3>
                            <p class="project-card__desc"><?= sanitize(truncate($project['description'], 110)) ?></p>
                            <div class="project-card__tech">
                                <?php foreach (array_slice($project['tech_array'], 0, 4) as $tech): ?>
                                    <span class="project-card__tech-item"><?= sanitize($tech) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <button class="project-card__btn" data-id="<?= $project['id'] ?>" onclick="openProjectFromPhp(this)" data-title="<?= sanitize($project['title']) ?>" data-desc="<?= sanitize($project['full_description'] ?? $project['description']) ?>" data-client="<?= sanitize($project['client'] ?? 'NOVA TECH') ?>" data-category="<?= sanitize($project['category']) ?>" data-tech="<?= sanitize(json_encode($project['tech_array'])) ?>">
                                View Details &rarr;
                            </button>
                        </div>
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
        <h2>Have A Project In Mind?</h2>
        <p>Let's make it real. Tell us what you want to build.</p>
        <a href="<?= base_url('contact.php') ?>" class="btn btn--outline">Start A Project</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<!-- Project details modal (client-side, data attributes from PHP) -->
<script>
function openProjectFromPhp(btn) {
    let techs = [];
    try { techs = JSON.parse(btn.getAttribute('data-tech') || '[]'); } catch (e) {}
    const techChips = techs.map(t => '<span>' + (window.escapeHtml ? window.escapeHtml(t) : t) + '</span>').join('');
    const html = '<div class="modal-detail">' +
        '<span class="modal-detail__category">' + (window.escapeHtml ? window.escapeHtml(btn.getAttribute('data-category')) : btn.getAttribute('data-category')) + '</span>' +
        '<h3>' + (window.escapeHtml ? window.escapeHtml(btn.getAttribute('data-title')) : btn.getAttribute('data-title')) + '</h3>' +
        '<div class="modal-detail__meta"><span><strong>Client:</strong> ' + (window.escapeHtml ? window.escapeHtml(btn.getAttribute('data-client')) : btn.getAttribute('data-client')) + '</span></div>' +
        '<p>' + (window.escapeHtml ? window.escapeHtml(btn.getAttribute('data-desc')) : btn.getAttribute('data-desc')) + '</p>' +
        '<h4>Technologies</h4><div class="tech-chips">' + techChips + '</div>' +
        '<div><a href="contact.php" class="btn btn--primary">Start A Similar Project</a></div>' +
        '</div>';
    if (window.openModal) { window.openModal(html); }
}
</script>