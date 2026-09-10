<?php
/**
 * NOVA TECH - Common Footer Component
 * 
 * This file is included at the bottom of every public PHP page.
 * It outputs the newsletter section, footer, and closing tags.
 * Class names match css/style.css.
 * 
 * Variables expected:
 *   $pdo - PDO object - Database connection
 */

$site_name = get_setting($pdo, 'site_name', 'NOVA TECH');
?>

<!-- NEWSLETTER SECTION  -->

<section class="newsletter section-pad" id="newsletter">
    <div class="container">
        <div class="newsletter__box">
            <h2 class="newsletter__title">Stay In The Loop</h2>
            <p class="newsletter__desc">
                Subscribe to our newsletter for the latest in development, AI and tech news.
            </p>
            <form class="newsletter__form" id="newsletter-form" novalidate>
                <?= csrf_field() ?>
                <input type="email" class="newsletter__input" id="newsletter-email" name="email" placeholder="Enter your email address" aria-label="Email address">
                <button type="submit" class="btn btn--primary">Subscribe</button>
            </form>
            <p class="newsletter__message" id="newsletter-message" aria-live="polite" hidden></p>
        </div>
    </div>
</section>

<!-- ============================================================
     FOOTER
============================================================ -->
<footer class="footer" id="footer">
    <div class="container">
        <div class="footer__grid">
            <!-- Brand column -->
            <div class="footer__col footer__col--brand">
                <a href="<?= base_url('index.php') ?>" class="navbar__logo footer__logo">
                    <span class="navbar__logo-icon" aria-hidden="true">N</span>
                    <span class="navbar__logo-text">NOVA<span>TECH</span></span>
                </a>
                <p class="footer__about">
                    NOVA TECH is a full-service technology company delivering web, mobile,
                    AI, security and cloud solutions to clients worldwide.
                </p>
                <div class="footer__social" aria-label="Social media links">
                    <a href="#" class="footer__social-link" aria-label="LinkedIn">in</a>
                    <a href="#" class="footer__social-link" aria-label="Twitter">t</a>
                    <a href="#" class="footer__social-link" aria-label="GitHub">gh</a>
                    <a href="#" class="footer__social-link" aria-label="Instagram">ig</a>
                </div>
            </div>

            <!-- Company links -->
            <div class="footer__col">
                <h4 class="footer__title">Company</h4>
                <ul class="footer__links">
                    <li><a href="<?= base_url('about.php') ?>">About Us</a></li>
                    <li><a href="<?= base_url('team.php') ?>">Our Team</a></li>
                    <li><a href="<?= base_url('blog.php') ?>">Blog</a></li>
                    <li><a href="<?= base_url('contact.php') ?>">Contact</a></li>
                </ul>
            </div>

            <!-- Services links -->
            <div class="footer__col">
                <h4 class="footer__title">Services</h4>
                <ul class="footer__links">
                    <li><a href="<?= base_url('services.php') ?>">Web Development</a></li>
                    <li><a href="<?= base_url('services.php') ?>">Mobile Apps</a></li>
                    <li><a href="<?= base_url('services.php') ?>">AI Solutions</a></li>
                    <li><a href="<?= base_url('services.php') ?>">Cyber Security</a></li>
                    <li><a href="<?= base_url('services.php') ?>">Cloud Solutions</a></li>
                </ul>
            </div>

            <!-- Contact links -->
            <div class="footer__col">
                <h4 class="footer__title">Contact</h4>
                <ul class="footer__links footer__links--contact">
                    <li><span><?= sanitize(get_setting($pdo, 'site_address', '123 Tech Avenue, San Francisco, CA')) ?></span></li>
                    <li><a href="tel:+15551234567"><?= sanitize(get_setting($pdo, 'site_phone', '+1 (555) 123-4567')) ?></a></li>
                    <li><a href="mailto:hello@novatech.com"><?= sanitize(get_setting($pdo, 'site_email', 'hello@novatech.com')) ?></a></li>
                </ul>
            </div>
        </div>

        <div class="footer__bottom">
            <p>&copy; <?= date('Y') ?> NOVA TECH. All rights reserved.</p>
            <div class="footer__bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

<!-- ============================================================
     SCRIPTS
============================================================ -->
<div id="toastContainer" class="toast-container"></div>
<script src="<?= base_url('js/main.js') ?>"></script>
<script src="<?= base_url('js/navbar.js') ?>"></script>

<!-- Newsletter AJAX handler -->
<script>
document.getElementById('newsletter-form').addEventListener('submit', async function (e) {
    e.preventDefault();
    const emailInput = document.getElementById('newsletter-email');
    const msgEl = document.getElementById('newsletter-message');

    if (!emailInput.value || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
        msgEl.textContent = 'Please enter a valid email address';
        msgEl.hidden = false;
        msgEl.style.color = 'var(--color-warm)';
        return;
    }

    const formData = new FormData(this);
    try {
        const res = await fetch('<?= base_url('api/newsletter.php') ?>', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();
        msgEl.hidden = false;
        if (data.success) {
            msgEl.style.color = '#00d2ff';
            msgEl.textContent = data.message;
            emailInput.value = '';
        } else {
            msgEl.style.color = 'var(--color-warm)';
            msgEl.textContent = data.message || 'Something went wrong.';
        }
    } catch (err) {
        msgEl.hidden = false;
        msgEl.style.color = 'var(--color-warm)';
        msgEl.textContent = 'Network error. Please try again.';
    }
});
</script>

</body>
</html>