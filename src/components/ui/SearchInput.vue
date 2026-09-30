<script setup>
/**
 * SearchInput - the live search box shared by projects / services / team / blog.
 *
 * Same controlled pattern as FilterBar: no internal state, it just reports
 * what the user typed.
 */

defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Search...' },
  // Lets a parent supply its own id so <label for="..."> still works.
  inputId: { type: String, default: 'search' },
  // The services page used a different CSS class name than the other pages.
  // Supporting both means neither stylesheet has to change.
  variant: { type: String, default: 'toolbar' },
})

const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <!--
    v-model on the native <input> does two things at once:
      1. :value="modelValue"
      2. @input="modelValue = $event.target.value"
    Here we intercept the input event ourselves so we can re-emit it upward.
  -->
  <input
    :id="inputId"
    type="search"
    :class="variant === 'services' ? 'services-search' : 'toolbar-search'"
    :value="modelValue"
    :placeholder="placeholder"
    aria-label="Search"
    @input="emit('update:modelValue', $event.target.value)"
  />
</template>
