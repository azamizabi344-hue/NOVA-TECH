import { onceVisible, stopWatching } from '@/composables/useInView'

/**
 * v-reveal        -> fades/slides the element up when it scrolls into view
 * v-reveal="2"    -> same, plus the .reveal-delay-2 class for a staggered grid
 *
 * Replaces this from main.js:
 *   const sectionHeads = document.querySelectorAll('.section-head')
 *   for (const head of sectionHeads) head.classList.add('reveal')
 *
 *   const gridGroups = document.querySelectorAll('.services__grid, ...')
 *   for (let i = 0; i < group.children.length; i++) {
 *     group.children[i].classList.add('reveal')
 *     if (i < 3) group.children[i].classList.add('reveal-delay-' + (i + 1))
 *   }
 *
 * Now each element opts in from its own markup, and it is impossible to forget
 * one because the class is applied by the directive, not by a global sweep.
 */
export const reveal = {
  // mounted fires once, right after the element is in the DOM.
  mounted(el, binding) {
    el.classList.add('reveal')

    // binding.value is whatever followed the directive: v-reveal="2" -> 2
    const delay = binding.value
    if (delay) {
      el.classList.add(`reveal-delay-${delay}`)
    }

    // The CSS does the animation; all we do is flip `revealed` on visibility.
    onceVisible(el, (node) => {
      node.classList.add('revealed')
    })
  },

  // Always clean up, otherwise the observer keeps removed nodes alive.
  unmounted(el) {
    stopWatching(el)
  },
}
