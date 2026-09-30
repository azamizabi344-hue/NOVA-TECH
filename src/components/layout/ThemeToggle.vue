<script setup>
import { useTheme } from '@/composables/useTheme'

const { isDark, toggleTheme } = useTheme()
</script>

<template>
  <!--
    There is deliberately no <style> block. The icon swap is already handled
    globally in style.css (.theme-toggle__icon--sun is hidden, and the
    html[data-theme="dark"] override reveals it) - so all this component has to
    do is supply the click handler and the ARIA state.
  -->
  <button
    class="theme-toggle"
    aria-label="Toggle dark mode"
    title="Toggle dark mode"
    :aria-pressed="isDark ? 'true' : 'false'"
    @click="toggleTheme"
  >
    <span class="theme-toggle__icon theme-toggle__icon--moon" aria-hidden="true">&#127769;</span>
    <span class="theme-toggle__icon theme-toggle__icon--sun" aria-hidden="true">&#9728;&#65039;</span>
  </button>
</template>
