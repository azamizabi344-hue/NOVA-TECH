<script setup>
import { ref } from 'vue'
import { useExplore } from '@/composables/useExplore'
import BlogCard from '@/components/cards/BlogCard.vue'
import SearchInput from '@/components/ui/SearchInput.vue'
import FilterBar from '@/components/ui/FilterBar.vue'
import ResultCount from '@/components/ui/ResultCount.vue'
import EmptyState from '@/components/ui/EmptyState.vue'
import { articles, blogFilters } from '@/data/blog'

/**
 * BlogExplorer - category filter + live search over the 10 articles.
 *
 * The last of the four explorers. Note there is NO nestedField here: unlike
 * projects (technologies), services (features) and team (skills), no article
 * has a nested array that the original searched. Passing one would be a silent
 * behaviour change, not an improvement.
 */
const { search, filter, filteredItems, isEmpty } = useExplore(ref(articles), {
  // These four fields are exactly what matchesArticleSearch() checked.
  searchFields: ['title', 'description', 'author', 'category'],
  filterField: 'category',
})
</script>

<template>
  <section class="blog-page section-pad">
    <div class="container">
      <!-- Same shape as the team page: search alone in a .toolbar, filter bar
           as its sibling, no sort dropdown. -->
      <div class="toolbar">
        <SearchInput
          v-model="search"
          input-id="blog-search"
          placeholder="Search articles, topics or authors..."
        />
      </div>

      <FilterBar v-model="filter" :options="blogFilters" />

      <ResultCount :count="filteredItems.length" singular="article" />

      <div class="blog__grid">
        <BlogCard
          v-for="article in filteredItems"
          :key="article.id"
          :article="article"
        />
      </div>

      <EmptyState :show="isEmpty">
        No articles match your search. Try a different keyword or category.
      </EmptyState>
    </div>
  </section>
</template>
