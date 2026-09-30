<script setup>
import PageHero from '@/components/layout/PageHero.vue'
import ContactForm from '@/components/contact/ContactForm.vue'
import { contactInfo } from '@/data/contact'
</script>

<template>
  <PageHero
    title="Contact Us"
    crumb="Contact"
    desc="Tell us about your project and we'll get back to you within one business day."
  />

  <section class="contact-page section-pad">
    <div class="container contact__grid">
      <!-- Left: contact information -->
      <div class="contact-info" aria-label="Contact information">
        <div
          v-for="(item, index) in contactInfo"
          :key="item.id"
          v-reveal="index < 3 ? index + 1 : 0"
          class="contact-info__card"
        >
          <div class="contact-info__icon" aria-hidden="true">{{ item.icon }}</div>

          <div>
            <h4>{{ item.title }}</h4>
            <p>{{ item.text }}</p>
          </div>
        </div>
      </div>

      <!-- Right: the form -->
      <ContactForm />
    </div>
  </section>
</template>
