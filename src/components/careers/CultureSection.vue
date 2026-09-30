<script setup>
import SectionHead from '@/components/ui/SectionHead.vue'
import { cultureValues } from '@/data/careers'
</script>

<template>
  <!--
    Note: this section has NO class of its own in the original - it is a bare
    <section class="section-pad">. The inner grid is .careers-culture__grid.
  -->
  <section class="section-pad">
    <div class="container">
      <SectionHead tag="Life At Nova" title="Why People Love Working Here" />

      <div class="careers-culture__grid">
        <!--
          Same .value-card markup as the About page's Core Values, different
          content. That reuse is the point: the original wrote these 4 cards
          from scratch even though they were structurally identical to the 4 it
          had already written on about.html.
        -->
        <div
          v-for="(value, index) in cultureValues"
          :key="value.id"
          v-reveal="index + 1"
          class="value-card"
        >
          <span class="value-card__icon">{{ value.icon }}</span>

          <h4>{{ value.title }}</h4>

          <p>{{ value.text }}</p>
        </div>
      </div>
    </div>
  </section>
</template>
