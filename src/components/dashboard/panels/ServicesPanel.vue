<script setup>
import { dashboardServices } from '@/data/dashboard'

/**
 * ServicesPanel - a read-only overview of the six service lines.
 *
 * Replaces renderServicesPanel(), which built the grid as a template string and
 * assigned grid.innerHTML. Vue needs no such step: the list is static data and
 * the markup is a v-for.
 */
</script>

<template>
  <div class="panel__header">
    <div>
      <h2 class="panel__title">Services</h2>
      <p class="panel__sub">An overview of what NOVA TECH offers.</p>
    </div>
  </div>

  <div class="dash-services">
    <div v-for="service in dashboardServices" :key="service.title" class="service-card">
      <div class="service-card__icon" aria-hidden="true">{{ service.icon }}</div>
      <h3 class="service-card__title">{{ service.title }}</h3>
      <p class="service-card__desc">{{ service.desc }}</p>
    </div>
  </div>
</template>
