<?php
/**
 * NOVA TECH - About Us page
 *
 * Story, mission, core values and the timeline stay as static content,
 * while the company statistics and leadership section load real data
 * from the database (projects / team_members tables).
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Real counts for the company statistics section
$projects_count = count_rows($pdo, "SELECT COUNT(*) as total FROM projects WHERE is_active = 1");
$team_count = count_rows($pdo, "SELECT COUNT(*) as total FROM team_members WHERE is_active = 1");

// Leadership = team members whose department is "Leadership" (fallback: first 4)
$leadership = fetch_all($pdo, "
    SELECT * FROM team_members 
    WHERE is_active = 1 AND department = 'Leadership' 
    ORDER BY sort_order ASC, id ASC 
    LIMIT 4
");
if (count($leadership) < 4) {
    $fallback = fetch_all($pdo, "
        SELECT * FROM team_members 
        WHERE is_active = 1 
        ORDER BY sort_order ASC, id ASC 
        LIMIT " . (4 - count($leadership)) . "
    ");
    $leadership = array_merge($leadership, $fallback);
}

$page_title = 'About Us';
$page_description = 'Learn about NOVA TECH - our story, mission, values and the team behind the work.';
include __DIR__ . '/includes/header.php';
?>

<!-- PAGE HERO -->
<section class="page-hero">
    <div class="container">
        <h1 class="page-hero__title">About NOVA TECH</h1>
        <p class="page-hero__desc">
            A full-service technology company built on curiosity, craft and trust.
        </p>
        <div class="breadcrumb">
            <a href="<?= base_url('index.php') ?>">Home</a> / <span>About</span>
        </div>
    </div>
</section>

<!-- COMPANY STORY -->
<section class="about-page section-pad">
    <div class="container about-page__inner">
        <div class="about-page__visual" aria-hidden="true">Our Journey</div>
        <div class="about-page__text">
            <span class="section-head__tag">Our Story</span>
            <h2>From Two Developers To A Global Team</h2>
            <p>
                NOVA TECH was founded in 2015 in a small shared office with just two
                laptops and a single mission: prove that small teams can ship software
                large companies would be proud of.
            </p>
            <p>
                Ten years later we are a <?= $team_count ?>+ person company with engineers,
                designers, data scientists and security specialists across 30+ countries.
                We have delivered over <?= $projects_count ?> projects — from startup MVPs to
                enterprise platforms — while keeping the same hands-on, quality-first
                mindset we started with.
            </p>
        </div>
    </div>
</section>

<!-- MISSION / VISION -->
<section class="mission section-pad">
    <div class="container">
        <div class="section-head">
            <span class="section-head__tag">Why We Exist</span>
            <h2 class="section-head__title">Mission &amp; Vision</h2>
        </div>
        <div class="mission-grid">
            <article class="mission-card">
                <div class="mission-card__icon" aria-hidden="true">&#127919;</div>
                <h3 class="mission-card__title">Our Mission</h3>
                <p>
                    Empower businesses of every size with accessible, reliable and
                    innovative technology that solves real problems — delivered on time
                    and on budget.
                </p>
            </article>
            <article class="mission-card">
                <div class="mission-card__icon" aria-hidden="true">&#128302;</div>
                <h3 class="mission-card__title">Our Vision</h3>
                <p>
                    A world where every company can harness modern technology to grow,
                    compete and create value — regardless of its size or budget.
                </p>
            </article>
            <article class="mission-card">
                <div class="mission-card__icon" aria-hidden="true">&#129309;</div>
                <h3 class="mission-card__title">Our Approach</h3>
                <p>
                    We pair senior expertise with deep listening. Every engagement starts
                    with understanding your goals, then we engineer the simplest solution
                    that reaches them.
                </p>
            </article>
        </div>
    </div>
</section>

<!-- CORE VALUES -->
<section class="core-values section-pad">
    <div class="container">
        <div class="section-head">
            <span class="section-head__tag">What Guides Us</span>
            <h2 class="section-head__title">Core Values</h2>
        </div>
        <div class="values-grid">
            <div class="value-card">
                <span class="value-card__icon">&#10024;</span>
                <h4>Innovation</h4>
                <p>We explore new tools and ideas before they become trends.</p>
            </div>
            <div class="value-card">
                <span class="value-card__icon">&#128274;</span>
                <h4>Integrity</h4>
                <p>We are transparent with our process, pricing and timelines.</p>
            </div>
            <div class="value-card">
                <span class="value-card__icon">&#129309;</span>
                <h4>Collaboration</h4>
                <p>We treat our clients as partners, not tickets to close.</p>
            </div>
            <div class="value-card">
                <span class="value-card__icon">&#127947;&#65039;</span>
                <h4>Excellence</h4>
                <p>We sweat the details, because the details become the product.</p>
            </div>
        </div>
    </div>
</section>

<!-- COMPANY STATISTICS (live numbers from the database) -->
<section class="stats section-pad">
    <div class="container">
        <div class="stats__grid">
            <div class="stat-card">
                <span class="stat-card__number counter" data-target="<?= $projects_count ?>">0</span><span class="stat-card__suffix">+</span>
                <h4 class="stat-card__label">Projects Completed</h4>
            </div>
            <div class="stat-card">
                <span class="stat-card__number counter" data-target="<?= $team_count ?>">0</span><span class="stat-card__suffix">+</span>
                <h4 class="stat-card__label">Team Members</h4>
            </div>
            <div class="stat-card">
                <span class="stat-card__number counter" data-target="30">0</span><span class="stat-card__suffix">+</span>
                <h4 class="stat-card__label">Countries Served</h4>
            </div>
            <div class="stat-card">
                <span class="stat-card__number counter" data-target="6">0</span><span class="stat-card__suffix"></span>
                <h4 class="stat-card__label">Years of Experience</h4>
            </div>
        </div>
    </div>
</section>

<!-- TIMELINE -->
<section class="timeline-section section-pad">
    <div class="container">
        <div class="section-head">
            <span class="section-head__tag">Milestones</span>
            <h2 class="section-head__title">Our Journey So Far</h2>
        </div>
        <div class="timeline">
            <div class="timeline__item">
                <span class="timeline__year">2015</span>
                <h3 class="timeline__title">NOVA TECH is founded</h3>
                <p class="timeline__desc">Two developers begin building websites in a shared office.</p>
            </div>
            <div class="timeline__item">
                <span class="timeline__year">2017</span>
                <h3 class="timeline__title">Mobile development launch</h3>
                <p class="timeline__desc">We ship our first iOS and Android apps and pass 50 clients.</p>
            </div>
            <div class="timeline__item">
                <span class="timeline__year">2019</span>
                <h3 class="timeline__title">AI &amp; Data division</h3>
                <p class="timeline__desc">A dedicated team starts building machine-learning products.</p>
            </div>
            <div class="timeline__item">
                <span class="timeline__year">2020</span>
                <h3 class="timeline__title">Cyber security practice</h3>
                <p class="timeline__desc">Security audits and penetration testing become core services.</p>
            </div>
            <div class="timeline__item">
                <span class="timeline__year">2022</span>
                <h3 class="timeline__title">Global cloud scale</h3>
                <p class="timeline__desc">Cloud solutions across 30+ countries and multi-region deployments.</p>
            </div>
            <div class="timeline__item">
                <span class="timeline__year">Today</span>
                <h3 class="timeline__title"><?= $projects_count ?>+ projects delivered</h3>
                <p class="timeline__desc">A <?= $team_count ?>+ person team serving clients worldwide with 98% satisfaction.</p>
            </div>
        </div>
    </div>
</section>

<!-- LEADERSHIP -->
<?php if (!empty($leadership)): ?>
<section class="leadership section-pad">
    <div class="container">
        <div class="section-head">
            <span class="section-head__tag">Leadership</span>
            <h2 class="section-head__title">People Who Lead The Way</h2>
        </div>
        <div class="leadership__grid">
            <?php foreach ($leadership as $leader): ?>
                <article class="leader-card">
                    <div class="leader-card__photo" <?= $leader['photo'] ? 'style="background-image:url(\'' . base_url('uploads/team/' . $leader['photo']) . '\');background-size:cover;background-position:center;font-size:0;"' : '' ?>>
                        <?= !$leader['photo'] ? sanitize(substr($leader['full_name'], 0, 1)) : '' ?>
                    </div>
                    <h3 class="leader-card__name"><?= sanitize($leader['full_name']) ?></h3>
                    <div class="leader-card__role"><?= sanitize($leader['role']) ?></div>
                    <p><?= sanitize(truncate($leader['bio'] ?? '', 110)) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA STRIP -->
<section class="cta-strip">
    <div class="container">
        <h2>Want To Build Something Great Together?</h2>
        <p>Let's talk about your project — no strings attached.</p>
        <a href="<?= base_url('contact.php') ?>" class="btn btn--outline">Get In Touch</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>