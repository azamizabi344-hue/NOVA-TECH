<script setup>
import SectionHead from '@/components/ui/SectionHead.vue'
import { timeline } from '@/data/about'
</script>

<template>
  <section class="timeline-section section-pad">
    <div class="container">
      <SectionHead tag="Milestones" title="Our Journey So Far" />

      <div class="timeline">
        <!--
          `year` is a string, not a Date. The last entry is literally "Today",
          so this list can never be chronologically sorted by a numeric
          comparison without special-casing it - which is why it is data and
          rendered in array order.
        -->
        <div
          v-for="(entry, index) in timeline"
          :key="entry.id"
          v-reveal="index < 3 ? index + 1 : 0"
          class="timeline__item"
        >
          <span class="timeline__year">{{ entry.year }}</span>

          <h3 class="timeline__title">{{ entry.title }}</h3>

          <p class="timeline__desc">{{ entry.desc }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
