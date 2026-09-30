<script setup>
import { computed } from 'vue'

/**
 * ServiceIcon - the six service SVGs.
 *
 * In the original project these lived in services.js as raw SVG STRINGS and
 * were injected with innerHTML / v-html. That works, but it means the markup is
 * invisible to Vue, to the compiler and to your editor.
 *
 * Here they are plain SVG inside a normal template, chosen with v-if. Nothing
 * is ever injected as raw HTML, so there is no XSS surface at all.
 *
 * The SVG attributes match index.html exactly (viewBox, size, stroke), so the
 * icons look identical to the original.
 */

const props = defineProps({
  // one of: web | mobile | ai | security | cloud | design
  name: { type: String, required: true },
})

// Computed so unknown names fall back to the "web" chevrons instead of
// rendering nothing at all.
const KNOWN = ['web', 'mobile', 'ai', 'security', 'cloud', 'design']
const icon = computed(() => (KNOWN.includes(props.name) ? props.name : 'web'))
</script>

<template>
  <span aria-hidden="true">
    <svg
      v-if="icon === 'web'"
      viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor"
      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
    >
      <polyline points="16 18 22 12 16 6"></polyline>
      <polyline points="8 6 2 12 8 18"></polyline>
    </svg>

    <svg
      v-else-if="icon === 'mobile'"
      viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor"
      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
    >
      <rect x="5" y="2" width="14" height="20" rx="2"></rect>
      <line x1="12" y1="18" x2="12.01" y2="18"></line>
    </svg>

    <svg
      v-else-if="icon === 'ai'"
      viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor"
      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
    >
      <path d="M12 2a4 4 0 0 1 4 4 4 4 0 0 1 4 4 4 4 0 0 1-4 4 4 4 0 0 1-4 4 4 4 0 0 1-4-4 4 4 0 0 1-4-4 4 4 0 0 1 4-4 4 4 0 0 1 4-4z"></path>
    </svg>

    <svg
      v-else-if="icon === 'security'"
      viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor"
      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
    >
      <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
      <polyline points="9 12 11 14 15 10"></polyline>
    </svg>

    <svg
      v-else-if="icon === 'cloud'"
      viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor"
      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
    >
      <path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path>
    </svg>

    <svg
      v-else-if="icon === 'design'"
      viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor"
      stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
    >
      <circle cx="13.5" cy="6.5" r="2.5"></circle>
      <circle cx="17.5" cy="10.5" r="2.5"></circle>
      <circle cx="8.5" cy="7.5" r="2.5"></circle>
      <circle cx="6.5" cy="12.5" r="2.5"></circle>
      <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"></path>
    </svg>
  </span>
</template>
