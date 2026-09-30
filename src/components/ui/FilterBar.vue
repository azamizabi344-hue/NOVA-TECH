<script setup>
/**
 * FilterBar - the row of category chips used by 5 places in the original
 * project: #project-filters (index + projects), #service-filters,
 * #team-filters, #blog-filters.
 *
 * It is fully controlled: it holds NO state. The parent owns `filter` and
 * passes it in; clicking a button emits the new value back up. That is the
 * v-model contract.
 */

// defineProps declares what this component accepts.
defineProps({
  // The current value. Named modelValue so v-model works.
  modelValue: { type: String, default: 'all' },
  // [{ value: 'all', label: 'All' }, ...]
  options: { type: Array, required: true },
})

// defineEmits declares what this component can send back up.
const emit = defineEmits(['update:modelValue'])
</script>

<template>
  <div class="filter-bar">
    <!--
      v-for turns an array into repeated markup. :key must be unique and
      stable - here the category value is perfect.
    -->
    <button
      v-for="option in options"
      :key="option.value"
      type="button"
      class="filter-btn"
      :class="{ active: option.value === modelValue }"
      :aria-pressed="option.value === modelValue ? 'true' : 'false'"
      @click="emit('update:modelValue', option.value)"
    >
      {{ option.label }}
    </button>
  </div>
</template>
