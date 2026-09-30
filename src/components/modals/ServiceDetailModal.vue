<script setup>
import { computed } from 'vue'
import { serviceCategoryLabels } from '@/data/services'

/**
 * ServiceDetailModal - the CONTENT of the shared modal for one service.
 *
 * Replaces openServiceDetails() from the original services.js.
 */
const props = defineProps({
  service: { type: Object, required: true },
})

// Replaces the 9-line switch statement that mapped a category to a display
// label. If the category is somehow unknown, fall back to the raw value -
// exactly what the original's `default:` branch did.
const categoryLabel = computed(
  () => serviceCategoryLabels[props.service.category] ?? props.service.category,
)
</script>

<template>
  <div class="modal-detail">
    <span class="modal-detail__category">{{ categoryLabel }}</span>

    <h3>{{ service.title }}</h3>

    <p>{{ service.longDescription }}</p>

    <h4>What's included</h4>

    <ul class="modal-detail__features">
      <li v-for="feature in service.features" :key="feature">{{ feature }}</li>
    </ul>

    <div class="price-tag">Starting at ${{ service.startingPrice.toLocaleString() }}</div>

    <div>
      <!-- contact.html becomes the /contact route -->
      <router-link to="/contact" class="btn btn--primary">Request A Quote</router-link>
    </div>
  </div>
</template>
