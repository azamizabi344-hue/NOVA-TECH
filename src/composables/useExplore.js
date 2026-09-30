import { ref, computed } from 'vue'
import { matchesTerm } from '@/utils/strings'

/**
 * useExplore - the shared search + category-filter logic.
 *
 * The original project implemented this four separate times:
 *   blog.js     blogState    = { search, filter }  + renderFilteredArticles()
 *   projects.js projectState = { search, filter }  + renderProjectList()
 *   services.js state        = { searchTerm } + activeFilter + renderServices()
 *   team.js     teamState    = { search, filter }  + renderTeamList()
 *
 * Each one had its own hand-written filter pipeline and its own render function
 * that rebuilt innerHTML. Here the pipeline lives once, and Vue's reactivity
 * does the re-rendering for free.
 *
 * @param {import('vue').Ref<Array>} items        the full list
 * @param {object}   options
 * @param {string[]} options.searchFields  keys to match the search term against
 * @param {string}   options.filterField   the key the filter compares to
 * @param {Function} [options.nestedField] optional array key inside each item
 *                                        to also search (technologies, skills)
 */
export function useExplore(items, options = {}) {
  const {
    searchFields = ['name', 'title'],
    filterField = 'category',
    nestedField = null,
  } = options

  // Two refs. Typing or clicking a chip changes them and everything below
  // recomputes automatically.
  const search = ref('')
  const filter = ref('all')

  const filteredItems = computed(() => {
    // Normalise once: lowercase and trimmed, so the matcher stays simple.
    const term = search.value.trim().toLowerCase()

    return items.value.filter((item) => {
      // 1. Does it pass the active category chip?
      //    'all' means no category constraint at all.
      if (filter.value !== 'all' && item[filterField] !== filter.value) {
        return false
      }

      // 2. Does it match the search text? (empty search matches everything)
      if (!term) return true

      const direct = searchFields.map((field) => item[field])
      const foundDirect = matchesTerm(direct, term)

      // 3. Also look inside the nested array, if the data has one.
      //    This replaces the `while` loop in projects.js/team.js that
      //    looped over item.technologies looking for a match.
      if (foundDirect) return true
      if (nestedField && Array.isArray(item[nestedField])) {
        return matchesTerm(item[nestedField], term)
      }

      return false
    })
  })

  const isEmpty = computed(() => filteredItems.value.length === 0)

  function reset() {
    search.value = ''
    filter.value = 'all'
  }

  return { search, filter, filteredItems, isEmpty, reset }
}
