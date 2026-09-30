<script setup>
import Counter from '@/components/ui/Counter.vue'

/**
 * StatCard - one animated statistic tile.
 * Used by the homepage stats section and the about page stats section.
 *
 * The original markup:
 *   <div class="stat-card">
 *     <span class="stat-card__number counter" data-target="150">0</span>
 *     <span class="stat-card__suffix">+</span>
 *     <h4 class="stat-card__label">Projects Completed</h4>
 *   </div>
 */
defineProps({
  value: { type: Number, required: true },
  suffix: { type: String, default: '' },
  label: { type: String, required: true },
  // stagger index 1/2/3 for the reveal delay; 0 = no delay
  delay: { type: Number, default: 0 },
})
</script>

<template>
  <!--
    v-reveal="delay" adds .reveal plus .reveal-delay-N (only when delay is
    truthy). This is the Vue version of main.js's stagger loop:
      if (i < 3) group.children[i].classList.add('reveal-delay-' + (i + 1))
  -->
  <div v-reveal="delay" class="stat-card">
    <Counter :target="value" class="stat-card__number" />
    <span class="stat-card__suffix">{{ suffix }}</span>
    <h4 class="stat-card__label">{{ label }}</h4>
  </div>
</template>
