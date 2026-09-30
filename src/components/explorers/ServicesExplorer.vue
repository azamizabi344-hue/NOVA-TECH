<script setup>
import { ref } from 'vue'
import { useExplore } from '@/composables/useExplore'
import ServiceCard from '@/components/cards/ServiceCard.vue'
import SearchInput from '@/components/ui/SearchInput.vue'
import FilterBar from '@/components/ui/FilterBar.vue'
import ResultCount from '@/components/ui/ResultCount.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import { services, serviceFilters } from '@/data/services'

/**
 * ServicesExplorer - search + 7 category chips over the 6 services.
 *
 * The third of four near-identical implementations in the original project.
 * Compare what this file does NOT contain, versus services.js:
 *
 *   - no buildServiceCard()          -> <ServiceCard v-for>
 *   - no renderServices()            -> filteredItems computed
 *   - no matchesSearch()             -> useExplore
 *   - no initServiceFilters()        -> <FilterBar v-model>
 *   - no initServiceSearch()         -> <SearchInput v-model>
 *   - no openServiceDetails()        -> <ServiceDetailModal>
 *   - no observeReveal() call        -> v-reveal handles it
 */
const { search, filter, filteredItems, isEmpty } = useExplore(ref(services), {
  searchFields: ['title', 'description'],
  filterField: 'category',
  // Replaces the for...of loop that scanned service.features for the term.
  nestedField: 'features',
})
</script>

<template>
  <section class="services-page section-pad">
    <div class="container">
      <!--
        NOTE the class difference: this page uses .services-toolbar and
        .services-search, while projects/team/blog use .toolbar and
        .toolbar-search. Both stylesheets already existed, so SearchInput's
        variant prop picks the right one instead of changing any CSS.
      -->
      <div class="services-toolbar">
        <SearchInput
          v-model="search"
          variant="services"
          input-id="service-search"
          placeholder="Search services, e.g. AI..."
        />

        <FilterBar v-model="filter" :options="serviceFilters" />
      </div>

      <ResultCount :count="filteredItems.length" singular="service" />

      <!-- .services-detail-grid, NOT .services__grid from the homepage. -->
      <div class="services-detail-grid">
        <ServiceCard
          v-for="service in filteredItems"
          :key="service.id"
          :service="service"
        />
      </div>

      <EmptyState :show="isEmpty">
        No services match your search. Try a different keyword.
      </EmptyState>
    </div>
  </section>
</template>
