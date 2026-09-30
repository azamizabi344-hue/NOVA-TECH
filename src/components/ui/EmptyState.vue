<script setup>
/**
 * EmptyState - the "no results" message shown when a filter/search finds nothing.
 *
 * The original CSS was:
 *   .services-empty { display: none }
 *   .services-empty.show { display: block }
 * and JS did emptyBox.classList.toggle('show', list.length === 0).
 *
 * In Vue, v-if does that job far better: the node simply is not created, which
 * is both clearer and slightly faster than hiding it with CSS.
 */

defineProps({
  show: { type: Boolean, default: false },
  message: { type: String, default: 'No results found.' },
})
</script>

<template>
  <div v-if="show" class="services-empty">
    <slot>{{ message }}</slot>
  </div>
</template>
