<script setup>
import SectionHead from '@/components/ui/SectionHead.vue'
import { coreValues } from '@/data/about'
</script>

<template>
  <section class="core-values section-pad">
    <div class="container">
      <SectionHead tag="What Guides Us" title="Core Values" />

      <div class="values-grid">
        <!--
          Note the heading level: value-card uses <h4>, not <h3>. The original
          had h4 here inside a section whose SectionHead renders an <h2>, which
          skips a level. Preserved rather than "corrected" - changing it would
          alter the document outline the original authors chose.
        -->
        <div
          v-for="(value, index) in coreValues"
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
