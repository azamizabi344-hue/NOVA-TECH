import { createRouter, createWebHistory } from 'vue-router'
import { readSession } from '@/utils/storage'
import { SESSION_KEY } from '@/composables/useAuth'

// One route per page of the original project.
// `component: () => import(...)` is lazy loading: the page file is only
// downloaded from the server when you actually visit that page.
const routes = [
  { path: '/',          name: 'home',      component: () => import('@/views/HomeView.vue') },
  { path: '/about',     name: 'about',     component: () => import('@/views/AboutView.vue') },
  { path: '/services',  name: 'services',  component: () => import('@/views/ServicesView.vue') },
  { path: '/projects',  name: 'projects',  component: () => import('@/views/ProjectsView.vue') },
  { path: '/team',      name: 'team',      component: () => import('@/views/TeamView.vue') },
  { path: '/blog',      name: 'blog',      component: () => import('@/views/BlogView.vue') },
  { path: '/contact',   name: 'contact',   component: () => import('@/views/ContactView.vue') },
  { path: '/careers',   name: 'careers',   component: () => import('@/views/CareersView.vue') },
  { path: '/login',     name: 'login',     component: () => import('@/views/LoginView.vue') },

  // meta.chrome = false tells App.vue to skip <NavBar> and <SiteFooter>,
  // because the dashboard brings its own sidebar shell (like the original
  // dashboard.html, which never had the shared navbar or footer).
  //
  // meta.requiresAuth is the dashboard's guard. The original did this inside
  // dashboard.js on DOMContentLoaded, which meant the dashboard markup rendered
  // first and the visitor was redirected a moment later. A router guard runs
  // BEFORE the view component is created, so there is no flash.
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('@/views/DashboardView.vue'),
    meta: { chrome: false, requiresAuth: true },
  },
]

const router = createRouter({
  // Turns /about into the "about" route. Vite's dev server handles this
  // automatically, so refreshing /about works in development.
  history: createWebHistory(),
  routes,
  // When you click a <router-link>, scroll back to the top of the new page
  // instead of staying halfway down (native anchors do this for free).
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) return savedPosition
    return { top: 0 }
  },
})

/**
 * Auth guard.
 *
 * Reads the session straight from sessionStorage rather than from the composable
 * on purpose: `currentUser` is a module-level ref initialised once when
 * useAuth.js is first imported. A logout in another tab (or a stale ref after a
 * manual sessionStorage.clear()) would leave that ref populated, and the guard
 * would wave the visitor through. Reading storage here is always truthful.
 */
router.beforeEach((to) => {
  if (!to.meta.requiresAuth) return true

  const session = readSession(SESSION_KEY, null)
  if (session?.user) return true

  // No session: send them to the login page, and remember where they were going
  // so they can be returned there after signing in.
  return { name: 'login', query: { redirect: to.fullPath } }
})

export default router