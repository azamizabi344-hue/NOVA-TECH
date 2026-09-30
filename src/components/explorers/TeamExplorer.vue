<script setup>
import { ref } from 'vue'
import { useExplore } from '@/composables/useExplore'
import TeamCard from '@/components/cards/TeamCard.vue'
import SearchInput from '@/components/ui/SearchInput.vue'
import FilterBar from '@/components/ui/FilterBar.vue'
import ResultCount from '@/components/ui/ResultCount.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import { team, teamFilters } from '@/data/team'

/**
 * TeamExplorer - department filter + live search over all 10 members.
 *
 * The fourth and last of the four hand-rolled explorers. The original
 * team.js had its own teamState object, its own matchesMemberSearch(), its own
 * renderTeamList() and its own filter/search wiring - all of which is now the
 * three lines below.
 */
const { search, filter, filteredItems, isEmpty } = useExplore(ref(team), {
  /**
   * IMPORTANT: these three fields are the ones the original searched. It is
   * tempting to add `bio` here for a "richer" search, but that would change
   * behaviour - "maria" would now match someone's bio. Keep it faithful.
   */
  searchFields: ['name', 'role', 'department'],
  filterField: 'department',
  // Replaces the for...of loop over member.skills.
  nestedField: 'skills',
})
</script>

<template>
  <section class="team-page section-pad">
    <div class="container">
      <!--
        The search box sits in its own .toolbar with no .toolbar__right beside
        it, because this page has no sort dropdown. The grid falls back to a
        single column automatically.
      -->
      <div class="toolbar">
        <SearchInput
          v-model="search"
          input-id="team-search"
          placeholder="Search by name, role or skill..."
        />
      </div>

      <!--
        On the services page the filter bar lives INSIDE .services-toolbar.
        Here it is a sibling, because that is where team.html had it. The CSS
        treats both arrangements identically.
      -->
      <FilterBar v-model="filter" :options="teamFilters" />

      <!--
        "team member" is one of the two cases where naively appending "s" to
        the singular would be wrong, so plural is passed explicitly.
        1 -> "1 team member found" / 5 -> "5 team members found"
      -->
      <ResultCount
        :count="filteredItems.length"
        singular="team member"
        plural="team members"
      />

      <div class="team__grid">
        <TeamCard
          v-for="member in filteredItems"
          :key="member.id"
          :member="member"
        />
      </div>

      <EmptyState :show="isEmpty">
        No team members match your search. Try a different keyword or department.
      </EmptyState>
    </div>
  </section>
</template>
