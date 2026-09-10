<?php
/**
 * NOVA TECH - Contact Page
 * 
 * Shows contact information and a message form.
 * The form is submitted via AJAX (fetch) to api/contact.php,
 * which saves it in the contact_messages table.
 * Flow:  JavaScript -> PHP (api/contact.php) -> MySQL
 */

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

// Pull contact details from the settings table
$site_phone  = get_setting($pdo, 'site_phone', '+1 (555) 123-4567');
$site_email  = get_setting($pdo, 'site_email', 'hello@novatech.com');
$site_address = get_setting($pdo, 'site_address', '123 Tech Avenue, San Francisco, CA');

$page_title = 'Contact';
$page_description = 'Tell us about your project and we\'ll get back to you within one business day.';
include __DIR__ . '/includes/header.php';
?>

<!-- ============================================================
     PAGE HERO
============================================================ -->
<section class="page-hero">
    <div class="container">
        <h1 class="page-hero__title">Contact Us</h1>
        <p class="page-hero__desc">
            Tell us about your project and we'll get back to you within one business day.
        </p>
        <div class="breadcrumb">
            <a href="<?= base_url('index.php') ?>">Home</a> / <span>Contact</span>
        </div>
    </div>
</section>

<!-- ============================================================
     CONTACT SECTION
============================================================ -->
<section class="contact-page section-pad">
    <div class="container contact__grid">

        <!-- Left: contact information -->
        <div class="contact-info" aria-label="Contact information">
            <div class="contact-info__card">
                <div class="contact-info__icon" aria-hidden="true">&#128205;</div>
                <div>
                    <h4>Visit Us</h4>
                    <p><?= sanitize($site_address) ?></p>
                </div>
            </div>
            <div class="contact-info__card">
                <div class="contact-info__icon" aria-hidden="true">&#128222;</div>
                <div>
                    <h4>Call Us</h4>
                    <p><?= sanitize($site_phone) ?></p>
                </div>
            </div>
            <div class="contact-info__card">
                <div class="contact-info__icon" aria-hidden="true">&#9993;</div>
                <div>
                    <h4>Email Us</h4>
                    <p><?= sanitize($site_email) ?></p>
                </div>
            </div>
            <div class="contact-info__card">
                <div class="contact-info__icon" aria-hidden="true">&#9201;</div>
                <div>
                    <h4>Working Hours</h4>
                    <p>Mon - Fri, 9:00 AM - 6:00 PM</p>
                </div>
            </div>
        </div>

        <!-- Right: contact form (submitted via AJAX to api/contact.php) -->
        <div class="contact-form-wrap">
            <h2 class="form__title">Send Us A Message</h2>

            <!-- Success/error box -->
            <div class="form__success" id="contact-success" role="status" hidden></div>

            <form id="contact-form" novalidate>
                <?= csrf_field() ?>
                <div class="form__row">
                    <div class="form__group">
                        <label class="form__label" for="contact-name">Full Name <span class="form__required">*</span></label>
                        <input type="text" class="form__input" id="contact-name" name="name" placeholder="John Doe" autocomplete="name">
                        <p class="form__error"></p>
                    </div>
                    <div class="form__group">
                        <label class="form__label" for="contact-email">Email Address <span class="form__required">*</span></label>
                        <input type="email" class="form__input" id="contact-email" name="email" placeholder="john@example.com" autocomplete="email">
                        <p class="form__error"></p>
                    </div>
                </div>

                <div class="form__row">
                    <div class="form__group">
                        <label class="form__label" for="contact-phone">Phone Number</label>
                        <input type="tel" class="form__input" id="contact-phone" name="phone" placeholder="+1 555 123 4567" autocomplete="tel">
                        <p class="form__error"></p>
                    </div>
                    <div class="form__group">
                        <label class="form__label" for="contact-subject">Subject <span class="form__required">*</span></label>
                        <input type="text" class="form__input" id="contact-subject" name="subject" placeholder="Project inquiry">
                        <p class="form__error"></p>
                    </div>
                </div>

                <div class="form__group">
                    <label class="form__label" for="contact-message">Message <span class="form__required">*</span></label>
                    <textarea class="form__textarea" id="contact-message" name="message" placeholder="Tell us about your project..."></textarea>
                    <p class="form__error"></p>
                </div>

                <button type="submit" class="btn btn--primary btn--full" id="contact-submit">
                    Send Message
                </button>
            </form>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>

<!-- Contact form validation + AJAX submission -->
<script>
document.getElementById('contact-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = this;
    const submitBtn = document.getElementById('contact-submit');
    const successBox = document.getElementById('contact-success');

    // ----- Client-side validation -----
    const fields = {
        name: document.getElementById('contact-name'),
        email: document.getElementById('contact-email'),
        subject: document.getElementById('contact-subject'),
        message: document.getElementById('contact-message'),
    };
    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    let valid = true;

    // Clear previous errors
    form.querySelectorAll('.form__error').forEach(el => el.textContent = '');
    form.querySelectorAll('.form__input, .form__textarea').forEach(el => el.classList.remove('form__input--error'));

    if (!fields.name.value.trim()) { setError(fields.name, 'Please enter your name.'); valid = false; }
    if (!emailRe.test(fields.email.value.trim())) { setError(fields.email, 'Please enter a valid email.'); valid = false; }
    if (!fields.subject.value.trim()) { setError(fields.subject, 'Please enter a subject.'); valid = false; }
    if (fields.message.value.trim().length < 10) { setError(fields.message, 'Message must be at least 10 characters.'); valid = false; }

    if (!valid) return;

    // ----- Send to API via fetch (AJAX) -----
    submitBtn.disabled = true;
    submitBtn.textContent = 'Sending...';
    successBox.hidden = true;
    successBox.style.color = '';

    const formData = new FormData(form);
    const csrfInput = form.querySelector('input[name="csrf_token"]');

    try {
        const res = await fetch('<?= base_url('api/contact.php') ?>', {
            method: 'POST',
            headers: {
                'X-CSRF-Token': csrfInput ? csrfInput.value : '',
            },
            body: formData,
        });
        const data = await res.json();

        successBox.hidden = false;
        if (data.success) {
            successBox.classList.add('show');
            successBox.style.color = '#00d2ff';
            successBox.textContent = data.message;
            form.reset();
        } else {
            successBox.style.color = 'var(--color-warm)';
            successBox.textContent = data.message || 'Something went wrong. Please try again.';
        }
    } catch (err) {
        successBox.hidden = false;
        successBox.style.color = 'var(--color-warm)';
        successBox.textContent = 'Network error. Please try again.';
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send Message';
    }
});

function setError(input, message) {
    input.classList.add('form__input--error');
    const err = input.closest('.form__group').querySelector('.form__error');
    if (err) err.textContent = message;
}
</script>