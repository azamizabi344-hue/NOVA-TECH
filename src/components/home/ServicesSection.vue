<script setup>
import { computed } from 'vue'
import SectionHead from '@/components/ui/SectionHead.vue'
import ServiceIcon from '@/components/ui/ServiceIcon.vue'
import { services } from '@/data/services'

// 6 hardcoded <article class="service-card"> blocks in the original index.html
// are now one v-for over the shared services array.
const items = services
</script>

<template>
  <section class="services section-pad">
    <div class="container">
      <SectionHead
        tag="What We Do"
        title="Our Services"
        desc="End-to-end technology services designed to turn your ideas into reliable, scalable and secure products."
      />

      <div class="services__grid">
        <!--
          v-reveal="index < 3 ? index + 1 : 0" reproduces the original stagger
          on .services__grid children. The grid must stay a DIRECT parent of the
          cards because the reveal-delay styling assumes grid children.
        -->
        <article
          v-for="(service, index) in items"
          :key="service.id"
          v-reveal="index < 3 ? index + 1 : 0"
          class="service-card"
        >
          <div class="service-card__icon" aria-hidden="true">
            <ServiceIcon :name="service.icon" />
          </div>

          <h3 class="service-card__title">{{ service.shortTitle }}</h3>

          <p class="service-card__desc">{{ service.shortDesc }}</p>

          <router-link to="/services" class="service-card__link">
            Learn More &rarr;
          </router-link>
        </article>
      </div>
    </div>
  </section>
</template>
