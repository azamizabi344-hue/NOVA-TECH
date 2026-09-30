<script setup>
import { ref, computed } from 'vue'
import { useExplore } from '@/composables/useExplore'
import ProjectCard from '@/components/cards/ProjectCard.vue'
import SearchInput from '@/components/ui/SearchInput.vue'
import FilterBar from '@/components/ui/FilterBar.vue'
import ResultCount from '@/components/ui/ResultCount.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import { projects, projectFilters, projectSortOptions } from '@/data/projects'

/**
 * ProjectsExplorer - search + category filter + sort over all 12 projects.
 *
 * This one component replaces the bulk of the original projects.js:
 *
 *   const projectState = { search: '', filter: 'all' };   -> search + filter refs
 *   function matchesProjectSearch(project) { ... }         -> useExplore
 *   function compareProjects(a, b) { ... }                 -> the comparator below
 *   function renderFilteredProjects() { ...4 steps... }   -> two computeds
 *   function renderProjectList(...) { innerHTML = ... }    -> <ProjectCard v-for>
 *
 *   + 3 addEventListener blocks, each of which also had to manually manage the
 *     .active class on every sibling button.
 */

// useExplore expects a REF of the list, not the array itself - that is what
// lets it stay reactive. ref(projects) wraps the imported array; because it is
// a plain array (not reactive), no copy of the data is made.
const { search, filter, filteredItems, isEmpty } = useExplore(ref(projects), {
  // Searched by the visitor's typing.
  searchFields: ['name', 'description', 'client'],
  // The chip buttons compare against this key.
  filterField: 'category',
  // Also match inside the technologies array. This replaces the hand-written
  // `while` loop in matchesProjectSearch().
  nestedField: 'technologies',
})

// The sort dropdown is a third piece of state, so it gets its own ref.
const sort = ref('newest')

/**
 * The comparator. Array.prototype.sort() expects a function returning a
 * negative / zero / positive number. Returning a boolean (like some of the
 * original tutorials do) "works" but is undefined behaviour - this is correct.
 */
const comparators = {
  newest: (a, b) => b.year - a.year,
  oldest: (a, b) => a.year - b.year,
  az: (a, b) => a.name.localeCompare(b.name),
}

/**
 * filteredItems is category + search only. Sorting is a separate step so the
 * two concerns stay independent - the original interleaved them in one function.
 *
 * .slice() is essential: sort() mutates the array it is called on, and
 * filteredItems is a computed. Mutating it would be a Vue warning at best and
 * corrupted data at worst. slice() gives sort() a throwaway copy.
 */
const visibleProjects = computed(() =>
  filteredItems.value.slice().sort(comparators[sort.value] ?? comparators.newest),
)
</script>

<template>
  <section class="projects-page section-pad">
    <div class="container">
      <!-- Toolbar: search + sort -->
      <div class="toolbar">
        <SearchInput
          v-model="search"
          input-id="project-search"
          placeholder="Search by name, client or technology..."
        />

        <div class="toolbar__right">
          <label class="toolbar__label" for="project-sort">Sort by</label>

          <!--
            v-model on <select> works the same as on <input>, and
            v-for over projectSortOptions generates the <option>s, so the
            dropdown and its state can never drift apart.
          -->
          <select id="project-sort" v-model="sort" class="sort-select">
            <option v-for="option in projectSortOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>
        </div>
      </div>

      <FilterBar v-model="filter" :options="projectFilters" />

      <ResultCount :count="visibleProjects.length" singular="project" />

      <div class="projects__grid">
        <!--
          :key is the project id, NOT the array index. Vue uses the key to
          recognise which card is which, so deleting a search result removes
          only that node instead of re-patching every card after it. That is
          what keeps the reveal animations attached to the right elements.
        -->
        <ProjectCard
          v-for="project in visibleProjects"
          :key="project.id"
          :project="project"
        />
      </div>

      <EmptyState :show="isEmpty">
        No projects match your search. Try a different keyword or filter.
      </EmptyState>
    </div>
  </section>
</template>
