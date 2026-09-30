<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import DashboardSidebar from '@/components/dashboard/DashboardSidebar.vue'
import DashboardTopbar from '@/components/dashboard/DashboardTopbar.vue'
import OverviewPanel from '@/components/dashboard/panels/OverviewPanel.vue'
import ProjectsPanel from '@/components/dashboard/panels/ProjectsPanel.vue'
import ServicesPanel from '@/components/dashboard/panels/ServicesPanel.vue'
import TeamPanel from '@/components/dashboard/panels/TeamPanel.vue'
import IntentsPanel from '@/components/dashboard/panels/IntentsPanel.vue'
import MessagesPanel from '@/components/dashboard/panels/MessagesPanel.vue'
import UsersPanel from '@/components/dashboard/panels/UsersPanel.vue'
import SettingsPanel from '@/components/dashboard/panels/SettingsPanel.vue'

/**
 * DashboardView - the admin area.
 *
 * The original had initDashboard(user) wire up two logout buttons, the sidebar
 * and then call eight render*() functions in a row. Here:
 *   - logout is one function used by both buttons
 *   - the sidebar/topbar share a single `activePanel` ref
 *   - the eight panels are static imports resolved by a lookup, so there is
 *     nothing to "render"
 *
 * The auth guard itself lives in the router (see meta.requiresAuth there), which
 * is the only place that can redirect before a component is created - the
 * original did it inside dashboard.js on DOMContentLoaded, which meant the
 * dashboard markup briefly rendered for a logged-out visitor.
 */
const router = useRouter()
const { currentUser, logout } = useAuth()

const activePanel = ref('overview')

/** The panel component for the active id. Null if the id is unknown. */
const activeComponent = computed(
  () =>
    ({
      overview: OverviewPanel,
      projects: ProjectsPanel,
      services: ServicesPanel,
      team: TeamPanel,
      intents: IntentsPanel,
      messages: MessagesPanel,
      users: UsersPanel,
      settings: SettingsPanel,
    })[activePanel.value] ?? OverviewPanel,
)

function onLogout() {
  logout()
  router.push('/login')
}
</script>

<template>
  <div class="dashboard">
    <DashboardSidebar :active-panel="activePanel" @select="activePanel = $event" @logout="onLogout" />

    <main class="dashboard__main">
      <DashboardTopbar :user="currentUser" :active-panel="activePanel" @logout="onLogout" />

      <!--
        One <component :is> instead of eight <section> elements each toggling a
        .active class. No props are passed: each panel pulls what it needs from
        useDashboard() / useAuth(), which keeps the call site uniform.
        The router guard guarantees currentUser exists by the time this renders,
        but the v-if is a cheap safety net.
      -->
      <section v-if="currentUser" class="panel active">
        <component :is="activeComponent" />
      </section>
    </main>
  </div>
</template>
