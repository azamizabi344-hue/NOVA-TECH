<script setup>
import { useModal } from '@/composables/useModal'
import ServiceIcon from '@/components/ui/ServiceIcon.vue'
import ServiceDetailModal from '@/components/modals/ServiceDetailModal.vue'

/**
 * ServiceCard - one service tile on the services page.
 *
 * Replaces buildServiceCard() from the original services.js, which built the
 * whole card as a template string and separately re-bound every "Learn More"
 * button after each render.
 */
defineProps({
  service: { type: Object, required: true },
})

const { openModal } = useModal()

function showDetails(service) {
  openModal(ServiceDetailModal, { service })
}
</script>

<template>
  <!--
    Note the class list: service-card AND service-card--detail. The modifier is
    what gives the services-page grid its larger card style; the homepage grid
    omits it. Same base component, two densities.
  -->
  <article class="service-card service-card--detail card-enter" :data-id="service.id">
    <div class="service-card__icon" aria-hidden="true">
      <!-- A real component, not a v-html string of raw SVG. -->
      <ServiceIcon :name="service.icon" />
    </div>

    <h3 class="service-card__title">{{ service.title }}</h3>

    <p class="service-card__desc">{{ service.description }}</p>

    <ul class="service-card__features">
      <li v-for="feature in service.features" :key="feature">{{ feature }}</li>
    </ul>

    <!--
      toLocaleString() is a method, so it needs {{ }}. The original called it
      inside a template literal; same formatting either way ($1,499).
    -->
    <span class="price-tag">Starting at ${{ service.startingPrice.toLocaleString() }}</span>

    <button type="button" class="service-card__link service-card__learn" @click="showDetails(service)">
      Learn More &rarr;
    </button>
  </article>
</template>
