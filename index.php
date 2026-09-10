<?php
/**
 * NOVA TECH - Homepage
 * 
 * The homepage pulls real content from the MySQL database:
 * - Services  (services table)
 * - Projects  (projects table, is_featured = 1)
 * - Team      (team_members table)
 * - Testimonials (testimonials table)
 * - Pricing   (pricing_plans table)
 * Hero, About preview and FAQ remain as static markup.
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// ============================================================
// LOAD DATABASE CONTENT
// ============================================================
// Homepage services (up to 6, active ones first)
$home_services = fetch_all($pdo, "
    SELECT * FROM services 
    WHERE is_active = 1 
    ORDER BY sort_order ASC, id ASC 
    LIMIT 6
");

// Featured projects (up to 6)
$featured_projects = fetch_all($pdo, "
    SELECT * FROM projects 
    WHERE is_active = 1 AND is_featured = 1 
    ORDER BY sort_order ASC, id ASC 
    LIMIT 6
");
foreach ($featured_projects as &$fp) {
    $fp['tech_array'] = json_decode($fp['technologies'] ?? '[]', true) ?: [];
}
unset($fp);

// Featured team members (up to 4)
$featured_team = fetch_all($pdo, "
    SELECT * FROM team_members 
    WHERE is_active = 1 
    ORDER BY sort_order ASC, id ASC 
    LIMIT 4
");

// Testimonials (up to 3)
$testimonials = fetch_all($pdo, "
    SELECT * FROM testimonials 
    WHERE is_active = 1 
    ORDER BY sort_order ASC, id ASC 
    LIMIT 3
");

// Pricing plans
$pricing_plans = fetch_all($pdo, "
    SELECT * FROM pricing_plans 
    WHERE is_active = 1 
    ORDER BY sort_order ASC, id ASC
");
foreach ($pricing_plans as &$pp) {
    $pp['features_list'] = json_decode($pp['features'] ?? '[]', true) ?: [];
}
unset($pp);

// Real counts for the stats counters
$projects_count = count_rows($pdo, "SELECT COUNT(*) as total FROM projects WHERE is_active = 1");
$team_count = count_rows($pdo, "SELECT COUNT(*) as total FROM team_members WHERE is_active = 1");

$page_title = 'Home';
$page_description = 'NOVA TECH - Building the future of technology with web, mobile, AI and cloud solutions.';
include __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     HERO SECTION
============================================================ -->
<section class="hero" id="hero">
    <div class="hero__background" aria-hidden="true"></div>
    <div class="container hero__inner">
        <div class="hero__content">
            <span class="hero__badge">&#10022; Trusted by 150+ companies worldwide</span>
            <h1 class="hero__title">
                Building the <span class="text-gradient">Future</span> of Technology
            </h1>
            <p class="hero__subtitle">
                NOVA TECH designs and ships cutting-edge web platforms, mobile apps, AI
                systems and secure cloud solutions that help your business grow faster
                and smarter.
            </p>
            <div class="hero__buttons">
                <a href="<?= base_url('contact.php') ?>" class="btn btn--primary">Get Started</a>
                <a href="<?= base_url('projects.php') ?>" class="btn btn--outline">View Projects</a>
            </div>
            <div class="hero__stats">
                <div class="hero__stat">
                    <span class="hero__stat-number counter" data-target="<?= $projects_count ?>">0</span><span class="hero__stat-suffix">+</span>
                    <span class="hero__stat-label">Projects Delivered</span>
                </div>
                <div class="hero__stat">
                    <span class="hero__stat-number counter" data-target="<?= $team_count ?>">0</span><span class="hero__stat-suffix">+</span>
                    <span class="hero__stat-label">Team Members</span>
                </div>
                <div class="hero__stat">
                    <span class="hero__stat-number counter" data-target="30">0</span><span class="hero__stat-suffix">+</span>
                    <span class="hero__stat-label">Countries Served</span>
                </div>
            </div>
        </div>

        <!-- Technology visual -->
        <div class="hero__visual" aria-hidden="true">
            <div class="tech-visual">
                <div class="tech-visual__grid"></div>
                <div class="tech-visual__core">
                    <span class="tech-visual__icon">&#9883;</span>
                    <span class="tech-visual__icon tech-visual__icon--2">&#9670;</span>
                    <span class="tech-visual__icon tech-visual__icon--3">&#9679;</span>
                    <span class="tech-visual__icon tech-visual__icon--4">&#10022;</span>
                    <span class="tech-visual__ring tech-visual__ring--1"></span>
                    <span class="tech-visual__ring tech-visual__ring--2"></span>
                </div>
                <div class="tech-visual__card tech-visual__card--1">AI</div>
                <div class="tech-visual__card tech-visual__card--2">&lt;/&gt;</div>
                <div class="tech-visual__card tech-visual__card--3">01</div>
                <div class="tech-visual__card tech-visual__card--4">&#128274;</div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     STATS SECTION
============================================================ -->
<section class="stats section-pad" id="statistics">
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
                <h4 class="stat-card__label">Countries</h4>
            </div>
            <div class="stat-card">
                <span class="stat-card__number counter" data-target="98">0</span><span class="stat-card__suffix">%</span>
                <h4 class="stat-card__label">Client Satisfaction</h4>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     SERVICES SECTION (from database)
============================================================ -->
<section class="services section-pad" id="services">
    <div class="container">
        <div class="section-head">
            <span class="section-head__tag">What We Do</span>
            <h2 class="section-head__title">Our Services</h2>
            <p class="section-head__desc">
                End-to-end technology services designed to turn your ideas into reliable,
                scalable and secure products.
            </p>
        </div>
        <div class="services__grid">
            <?php foreach ($home_services as $service): ?>
                <article class="service-card">
                    <div class="service-card__icon" aria-hidden="true">
                        <i class="<?= sanitize($service['icon']) ?>" style="font-size:38px;"></i>
                    </div>
                    <h3 class="service-card__title"><?= sanitize($service['title']) ?></h3>
                    <p class="service-card__desc"><?= sanitize($service['short_description']) ?></p>
                    <a href="<?= base_url('services.php') ?>" class="service-card__link">Learn More &rarr;</a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     FEATURED PROJECTS SECTION (from database)
============================================================ -->
<section class="projects section-pad" id="projects">
    <div class="container">
        <div class="section-head">
            <span class="section-head__tag">Our Work</span>
            <h2 class="section-head__title">Featured Projects</h2>
            <p class="section-head__desc">
                A selection of products we have designed and engineered for clients
                around the world.
            </p>
        </div>

        <div class="projects__grid">
            <?php foreach ($featured_projects as $project): ?>
                <?php $image_url = $project['image'] ? base_url('uploads/projects/' . $project['image']) : null; ?>
                <article class="project-card card-enter">
                    <div class="project-card__image" <?= $image_url ? 'style="background-image:url(\'' . $image_url . '\');background-size:cover;background-position:center;"' : '' ?>>
                        <?php if (!$image_url): ?>
                            <span class="project-card__label"><?= sanitize(strtoupper(substr($project['title'], 0, 3))) ?></span>
                        <?php endif; ?>
                        <span class="project-card__category"><?= sanitize($project['category']) ?></span>
                    </div>
                    <div class="project-card__body">
                        <h3 class="project-card__title"><?= sanitize($project['title']) ?></h3>
                        <p class="project-card__desc"><?= sanitize(truncate($project['description'], 110)) ?></p>
                        <a href="<?= base_url('projects.php') ?>" class="project-card__btn">View Details &rarr;</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="projects__more">
            <a href="<?= base_url('projects.php') ?>" class="btn btn--outline">View All Projects</a>
        </div>
    </div>
</section>

<!-- ============================================================
     ABOUT SECTION
============================================================ -->
<section class="about section-pad" id="about">
    <div class="container about__inner">
        <div class="about__visual" aria-hidden="true">
            <div class="about__img-placeholder">
                <span>About NOVA TECH</span>
            </div>
        </div>
        <div class="about__content">
            <span class="section-head__tag">Who We Are</span>
            <h2 class="section-head__title">We Turn Big Ideas Into Working Products</h2>
            <p class="about__story">
                Founded in 2015, NOVA TECH has grown from a two-person studio into a
                full-service technology company. We partner with startups and enterprises
                to design, build and scale software that matters.
            </p>
            <div class="about__values">
                <div class="about__value">
                    <h4>Our Mission</h4>
                    <p>Empower businesses with accessible, reliable and innovative technology.</p>
                </div>
                <div class="about__value">
                    <h4>Our Vision</h4>
                    <p>A world where every company can harness modern technology to solve real problems.</p>
                </div>
            </div>
            <a href="<?= base_url('about.php') ?>" class="btn btn--primary">Learn More About Us</a>
        </div>
    </div>
</section>

<!-- ============================================================
     TEAM SECTION (from database)
============================================================ -->
<section class="team section-pad" id="team">
    <div class="container">
        <div class="section-head">
            <span class="section-head__tag">Who We Are</span>
            <h2 class="section-head__title">Talented Team Behind The Scenes</h2>
            <p class="section-head__desc">
                Meet some of the people who make NOVA TECH a world-class technology partner.
            </p>
        </div>
        <div class="team__grid">
            <?php foreach ($featured_team as $member): ?>
                <article class="team-card card-enter">
                    <div class="team-card__photo" <?= $member['photo'] ? 'style="background-image:url(\'' . base_url('uploads/team/' . $member['photo']) . '\');background-size:cover;background-position:center;font-size:0;"' : '' ?>>
                        <?= !$member['photo'] ? sanitize(substr($member['full_name'], 0, 1)) : '' ?>
                    </div>
                    <div class="team-card__info">
                        <span class="team-card__badge"><?= sanitize($member['department']) ?></span>
                        <h3 class="team-card__name"><?= sanitize($member['full_name']) ?></h3>
                        <div class="team-card__role"><?= sanitize($member['role']) ?></div>
                        <p class="team-card__bio"><?= sanitize(truncate($member['bio'] ?? '', 90)) ?></p>
                        <a href="<?= base_url('team.php') ?>" class="team-card__btn">View Profile &rarr;</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     TESTIMONIALS SECTION (from database)
============================================================ -->
<section class="testimonials section-pad" id="testimonials">
    <div class="container">
        <div class="section-head">
            <span class="section-head__tag">Client Feedback</span>
            <h2 class="section-head__title">What Our Clients Say</h2>
        </div>
        <div class="testimonials__grid">
            <?php foreach ($testimonials as $testimonial): ?>
                <article class="testimonial-card">
                    <div class="testimonial-card__stars" aria-label="<?= (int)$testimonial['rating'] ?> out of 5 stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span><?= $i <= $testimonial['rating'] ? '&#9733;' : '&#9734;' ?></span>
                        <?php endfor; ?>
                    </div>
                    <p class="testimonial-card__quote"><?= sanitize($testimonial['content']) ?></p>
                    <div class="testimonial-card__author">
                        <span class="testimonial-card__avatar"><?= sanitize(strtoupper(substr($testimonial['client_name'], 0, 1))) ?></span>
                        <div>
                            <h4><?= sanitize($testimonial['client_name']) ?></h4>
                            <span><?= sanitize($testimonial['client_role']) ?><?= $testimonial['company'] ? ', ' . sanitize($testimonial['company']) : '' ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     PRICING SECTION (from database)
============================================================ -->
<section class="pricing section-pad" id="pricing">
    <div class="container">
        <div class="section-head">
            <span class="section-head__tag">Pricing</span>
            <h2 class="section-head__title">Simple, Transparent Plans</h2>
            <p class="section-head__desc">
                Choose the plan that fits your stage. Scale up whenever you need more.
            </p>
        </div>
        <div class="pricing__grid">
            <?php foreach ($pricing_plans as $plan): ?>
                <article class="pricing-card <?= $plan['is_popular'] ? 'pricing-card--featured' : '' ?>">
                    <?php if ($plan['is_popular']): ?>
                        <span class="pricing-card__badge">Most Popular</span>
                    <?php endif; ?>
                    <h3 class="pricing-card__plan"><?= sanitize($plan['name']) ?></h3>
                    <p class="pricing-card__price">
                        <span class="pricing-card__currency">$</span>
                        <?= $plan['price'] > 0 ? number_format($plan['price']) : 'Custom' ?>
                        <span class="pricing-card__period">/<?= sanitize($plan['period']) ?></span>
                    </p>
                    <ul class="pricing-card__features">
                        <?php foreach ($plan['features_list'] as $feature): ?>
                            <li><?= sanitize($feature) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?= base_url('contact.php') ?>" class="btn <?= $plan['is_popular'] ? 'btn--primary' : 'btn--outline' ?> btn--full">Choose <?= sanitize($plan['name']) ?></a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     FAQ SECTION (accordion toggled by main.js)
============================================================ -->
<section class="faq section-pad" id="faq">
    <div class="container faq__inner">
        <div class="section-head">
            <span class="section-head__tag">Need Help?</span>
            <h2 class="section-head__title">Frequently Asked Questions</h2>
        </div>
        <div class="faq__list" id="faq-list">
            <div class="faq-item">
                <button class="faq-item__question" aria-expanded="false">
                    How long does a typical project take?
                    <span class="faq-item__icon" aria-hidden="true">+</span>
                </button>
                <div class="faq-item__answer">
                    <p>
                        A standard website takes 2-4 weeks, while a full web or mobile
                        application usually takes 6-12 weeks depending on scope. We give you a
                        clear timeline after our discovery call.
                    </p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-item__question" aria-expanded="false">
                    Do you provide support after launch?
                    <span class="faq-item__icon" aria-hidden="true">+</span>
                </button>
                <div class="faq-item__answer">
                    <p>
                        Yes. Every project includes a support period, and we also offer
                        ongoing maintenance plans to keep your product secure and up to date.
                    </p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-item__question" aria-expanded="false">
                    Can you work with our existing team?
                    <span class="faq-item__icon" aria-hidden="true">+</span>
                </button>
                <div class="faq-item__answer">
                    <p>
                        Absolutely. We frequently embed with in-house teams, review existing
                        codebases, and provide senior engineers for specific gaps.
                    </p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-item__question" aria-expanded="false">
                    What technologies do you specialize in?
                    <span class="faq-item__icon" aria-hidden="true">+</span>
                </button>
                <div class="faq-item__answer">
                    <p>
                        PHP, JavaScript, React, Node.js, Python, Flutter, cloud platforms
                        (AWS/Azure/GCP), Docker, Kubernetes and machine learning frameworks.
                    </p>
                </div>
            </div>
            <div class="faq-item">
                <button class="faq-item__question" aria-expanded="false">
                    How do we get started?
                    <span class="faq-item__icon" aria-hidden="true">+</span>
                </button>
                <div class="faq-item__answer">
                    <p>
                        Simply contact us through the form, tell us about your project, and
                        we will schedule a free discovery call within 24 hours.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>