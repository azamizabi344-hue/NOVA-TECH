<script setup>
import { ref, computed } from 'vue'
import SectionHead from '@/components/ui/SectionHead.vue'
import FilterBar from '@/components/ui/FilterBar.vue'
import ProjectCard from '@/components/cards/ProjectCard.vue'
import { projects, projectFilters, FEATURED_LIMIT } from '@/data/projects'

// ONE piece of state for the filter bar. In the original this was:
//   let featuredFilter = 'all';
const activeFilter = ref('all')

// computed = derived state. It is NOT stored anywhere; it is recalculated
// whenever activeFilter or projects changes.
//
// Original equivalent:
//   let featured = projectsData;
//   if (featuredFilter !== 'all') {
//     featured = projectsData.filter(p => p.category === featuredFilter);
//   }
//   featured = featured.slice(0, FEATURED_LIMIT);
const featuredProjects = computed(() => {
  const matching =
    activeFilter.value === 'all'
      ? projects
      : projects.filter((project) => project.category === activeFilter.value)

  // Note: .slice() returns a copy, so this can never mutate the source array.
  return matching.slice(0, FEATURED_LIMIT)
})
</script>

<template>
  <section class="projects section-pad">
    <div class="container">
      <SectionHead
        tag="Our Work"
        title="Featured Projects"
        desc="A selection of products we have designed and engineered for clients around the world."
      />

      <!--
        v-model on a component = :model-value + @update:model-value.
        Clicking a chip sets activeFilter, which recomputes featuredProjects,
        which re-renders the grid. No render() function call needed.
      -->
      <FilterBar v-model="activeFilter" :options="projectFilters" />

      <!-- v-for replaces grid.innerHTML = cardsHtml -->
      <div class="projects__grid">
        <ProjectCard
          v-for="project in featuredProjects"
          :key="project.id"
          :project="project"
        />
      </div>

      <div class="projects__more">
        <router-link to="/projects" class="btn btn--outline">View All Projects</router-link>
      </div>
    </div>
  </section>
</template>
