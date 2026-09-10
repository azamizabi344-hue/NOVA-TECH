<?php
/**
 * NOVA TECH - Team Page
 * 
 * Loads team members from the MySQL database (team_members table).
 * Supports server-side search and department filtering.
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// ============================================================
// SEARCH & DEPARTMENT FILTER
// ============================================================
$search = trim($_GET['q'] ?? '');
$dept_filter = trim($_GET['department'] ?? '');

$where = [];
$params = [];

if (!empty($search)) {
    $where[] = "(full_name LIKE ? OR role LIKE ? OR department LIKE ? OR skills LIKE ?)";
    $like = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

if (!empty($dept_filter)) {
    $where[] = "department = ?";
    $params[] = $dept_filter;
}

$where_sql = $where ? 'WHERE ' . implode(' AND ', $where) . ' AND is_active = 1' : 'WHERE is_active = 1';

$members = fetch_all($pdo, "
    SELECT * FROM team_members $where_sql 
    ORDER BY sort_order ASC, id ASC
", $params);

foreach ($members as &$m) {
    $m['skills_list'] = json_decode($m['skills'] ?? '[]', true) ?: [];
}
unset($m);

// Departments for the filter bar
$departments = fetch_all($pdo, "
    SELECT DISTINCT department FROM team_members 
    WHERE is_active = 1 
    ORDER BY department ASC
");

$page_title = 'Team';
$page_description = 'Meet the curious engineers, designers and specialists behind every NOVA TECH product.';
include __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     PAGE HERO
============================================================ -->
<section class="page-hero">
    <div class="container">
        <h1 class="page-hero__title">Meet The Team</h1>
        <p class="page-hero__desc">
            Curious engineers, designers and specialists — these are the people
            behind every NOVA TECH product.
        </p>
        <div class="breadcrumb">
            <a href="<?= base_url('index.php') ?>">Home</a> / <span>Team</span>
        </div>
    </div>
</section>

<!-- ============================================================
     TEAM (from MySQL database)
============================================================ -->
<section class="team-page section-pad">
    <div class="container">

        <!-- Toolbar: search + department filter -->
        <form class="toolbar" method="GET" action="<?= base_url('team.php') ?>">
            <input type="text" name="q" class="toolbar-search" placeholder="Search by name, role or skill..." aria-label="Search team members" value="<?= sanitize($search) ?>">
        </form>

        <div class="filter-bar">
            <a class="filter-btn <?= $dept_filter === '' ? 'active' : '' ?>" href="<?= base_url('team.php' . (!empty($search) ? '?q=' . urlencode($search) : '')) ?>">All</a>
            <?php foreach ($departments as $d): ?>
                <a class="filter-btn <?= $dept_filter === $d['department'] ? 'active' : '' ?>"
                   href="<?= base_url('team.php?department=' . urlencode($d['department']) . (!empty($search) ? '&q=' . urlencode($search) : '')) ?>">
                    <?= sanitize($d['department']) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Result count -->
        <p class="services-count"><?= count($members) ?> team member<?= count($members) === 1 ? '' : 's' ?> found</p>

        <!-- Cards rendered by PHP from the team_members table -->
        <div class="team__grid">
            <?php if (empty($members)): ?>
                <p class="services-empty show">No team members match your search. Try a different keyword or department.</p>
            <?php else: ?>
                <?php foreach ($members as $member): ?>
                    <article class="team-card card-enter">
                        <div class="team-card__photo" <?= $member['photo'] ? 'style="background-image:url(\'' . base_url('uploads/team/' . $member['photo']) . '\');background-size:cover;background-position:center;font-size:0;"' : '' ?>>
                            <?= !$member['photo'] ? sanitize(implode('', array_map(function($w){ return strtoupper(substr($w,0,1)); }, explode(' ', $member['full_name'])))) : '' ?>
                        </div>
                        <div class="team-card__info">
                            <span class="team-card__badge"><?= sanitize($member['department']) ?></span>
                            <h3 class="team-card__name"><?= sanitize($member['full_name']) ?></h3>
                            <div class="team-card__role"><?= sanitize($member['role']) ?></div>
                            <p class="team-card__bio"><?= sanitize(truncate($member['bio'] ?? '', 90)) ?></p>
                            <?php if (!empty($member['skills_list'])): ?>
                                <div class="team-card__skills">
                                    <?php foreach (array_slice($member['skills_list'], 0, 4) as $skill): ?>
                                        <span><?= sanitize($skill) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <button class="team-card__btn" onclick="openTeamFromPhp(this)" data-name="<?= sanitize($member['full_name']) ?>" data-role="<?= sanitize($member['role']) ?>" data-dept="<?= sanitize($member['department']) ?>" data-bio="<?= sanitize($member['bio'] ?? '') ?>" data-email="<?= sanitize($member['email'] ?? '') ?>" data-skills="<?= sanitize(json_encode($member['skills_list'])) ?>">View Profile &rarr;</button>
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
        <h2>Work With This Team</h2>
        <p>We are always happy to talk about your next project.</p>
        <a href="<?= base_url('contact.php') ?>" class="btn btn--outline">Contact Us</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
function openTeamFromPhp(btn) {
    let skills = [];
    try { skills = JSON.parse(btn.getAttribute('data-skills') || '[]'); } catch (e) {}
    const esc = (s) => window.escapeHtml ? window.escapeHtml(s) : s;
    const skillChips = skills.map(s => '<span class="team-card__skills" style="display:inline-block;padding:4px 12px;margin:4px 6px 0 0;font-size:12px;background:rgba(108,92,231,0.15);color:var(--color-primary-light);border-radius:999px;">' + esc(s) + '</span>').join('');
    const html = '<div class="modal-detail">' +
        '<span class="modal-detail__category">' + esc(btn.getAttribute('data-dept')) + '</span>' +
        '<h3>' + esc(btn.getAttribute('data-name')) + '</h3>' +
        '<div class="modal-detail__meta"><span><strong>Role:</strong> ' + esc(btn.getAttribute('data-role')) + '</span></div>' +
        (btn.getAttribute('data-email') ? '<p><strong>Email:</strong> <a href="mailto:' + esc(btn.getAttribute('data-email')) + '">' + esc(btn.getAttribute('data-email')) + '</a></p>' : '') +
        '<p>' + esc(btn.getAttribute('data-bio')) + '</p>' +
        '<h4>Skills</h4>' + (skillChips || '<p>None listed</p>') +
        '</div>';
    if (window.openModal) { window.openModal(html); }
}
</script>