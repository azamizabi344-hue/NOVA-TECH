<script setup>
/**
 * SectionHead - the tag + title + description block at the top of a section.
 *
 * It appears 20+ times across the original project with exactly the same three
 * elements. One component, used everywhere.
 *
 * Note v-reveal on the root: main.js used to loop over every .section-head and
 * add the reveal classes. Now each instance opts in itself, and can never be
 * missed because it is written in the markup.
 */

defineProps({
  tag: { type: String, default: '' },
  title: { type: String, required: true },
  desc: { type: String, default: '' },
})
</script>

<template>
  <div v-reveal class="section-head">
    <span v-if="tag" class="section-head__tag">{{ tag }}</span>
    <h2 class="section-head__title">{{ title }}</h2>
    <!-- v-if so sections without a description get no empty <p> -->
    <p v-if="desc" class="section-head__desc">{{ desc }}</p>
    <slot />
  </div>
</template>
