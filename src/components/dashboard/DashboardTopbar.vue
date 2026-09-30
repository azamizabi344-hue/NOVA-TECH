<script setup>
import { computed } from 'vue'
import ThemeToggle from '@/components/layout/ThemeToggle.vue'
import { panelTitles } from '@/data/dashboard'

/**
 * DashboardTopbar - the title bar above the panels.
 *
 * The original set two of these nodes by hand on load:
 *   document.getElementById('user-email').textContent  = user.email;
 *   document.getElementById('user-avatar').textContent = user.name.charAt(0).toUpperCase();
 *   document.getElementById('topbar-title').textContent = panelTitles[panelName];
 *
 * All three are now bindings. The title is a computed off the active panel, and
 * the avatar is `name.charAt(0).toUpperCase()` evaluated in place.
 */
const props = defineProps({
  user: { type: Object, required: true },
  activePanel: { type: String, required: true },
})

defineEmits(['logout'])

const title = computed(() => panelTitles[props.activePanel] ?? 'Dashboard')
const initial = computed(() => props.user.name.charAt(0).toUpperCase())
</script>

<template>
  <header class="dashboard__topbar">
    <h1 class="topbar__title">{{ title }}</h1>

    <div class="topbar__user">
      <!-- The dashboard has its own dark-mode button, same as the original. -->
      <ThemeToggle />

      <span class="topbar__avatar">{{ initial }}</span>
      <span>{{ user.email }}</span>

      <button type="button" class="btn btn--ghost btn--sm" @click="$emit('logout')">Logout</button>
    </div>
  </header>
</template>
