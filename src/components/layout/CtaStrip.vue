<script setup>
/**
 * CtaStrip - the "want to work with us?" band above the footer.
 *
 * This exact 7-line block appeared at the bottom of 6 different pages, each
 * with different wording:
 *   about.html     "Want To Build Something Great Together?"   -> Get In Touch
 *   blog.html      "Want To Work With Us?"                     -> See Careers
 *   careers.html   "Don't See Your Role?"                      -> Get In Touch
 *   projects.html  "Have A Project In Mind?"                   -> Start A Project
 *   services.html  "Need A Custom Solution?"                   -> Request A Quote
 *   team.html      "Work With This Team"                       -> Contact Us
 *
 * Six copies of the same markup, six hardcoded <a href="contact.html">. Now the
 * destination is data, so a page can point its CTA at /careers without anyone
 * hand-editing an href.
 */
defineProps({
  title: { type: String, required: true },
  desc: { type: String, default: '' },
  cta: { type: String, required: true },
  to: { type: String, default: '/contact' },
})
</script>

<template>
  <section class="cta-strip">
    <div class="container">
      <h2>{{ title }}</h2>

      <p v-if="desc">{{ desc }}</p>

      <router-link :to="to" class="btn btn--outline">{{ cta }}</router-link>
    </div>
  </section>
</template>
