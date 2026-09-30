<script setup>
import SectionHead from '@/components/ui/SectionHead.vue'
import { leadership } from '@/data/about'
</script>

<template>
  <section class="leadership section-pad">
    <div class="container">
      <SectionHead tag="Leadership" title="People Who Lead The Way" />

      <div class="leadership__grid">
        <!--
          Leadership is NOT team-card. The classes, the single-letter initial
          and the shorter bio are all different, so it gets its own markup here
          rather than reusing TeamCard (which would render the wrong styles).
        -->
        <article
          v-for="(person, index) in leadership"
          :key="person.id"
          v-reveal="index + 1"
          class="leader-card"
        >
          <div class="leader-card__photo">{{ person.initials }}</div>

          <h3 class="leader-card__name">{{ person.name }}</h3>

          <div class="leader-card__role">{{ person.role }}</div>

          <p>{{ person.text }}</p>
        </article>
      </div>
    </div>
  </section>
</template>
