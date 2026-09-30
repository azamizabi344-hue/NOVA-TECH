<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import { useRoute } from 'vue-router'
import ThemeToggle from './ThemeToggle.vue'
import { useAuth } from '@/composables/useAuth'

// --- STATE (was: JS variables + classList) ---------------------------------
const isScrolled = ref(false)   // adds .navbar--scrolled
const isMenuOpen = ref(false)   // adds .open to the menu, .active to the button

const route = useRoute()
const { isLoggedIn } = useAuth()

// --- DATA (was: 7 hardcoded <li> elements) ---------------------------------
// v-for replaces the copy-pasted markup. Change this array, change the nav.
const navLinks = [
  { label: 'Home',     to: '/' },
  { label: 'About',    to: '/about' },
  { label: 'Services', to: '/services' },
  { label: 'Projects', to: '/projects' },
  { label: 'Team',     to: '/team' },
  { label: 'Blog',     to: '/blog' },
  { label: 'Contact',  to: '/contact' },
]

function closeMenu() {
  isMenuOpen.value = false
}

function onScroll() {
  isScrolled.value = window.scrollY > 10
}

function onKeydown(event) {
  if (event.key === 'Escape') closeMenu()
}

// --- LIFECYCLE ---------------------------------------------------------------
// onMounted runs after the component is added to the page (replacing the old
// DOMContentLoaded). onUnmounted cleans up - without it, every page change
// would add ANOTHER scroll listener and the leak would compound.
onMounted(() => {
  window.addEventListener('scroll', onScroll)
  document.addEventListener('keydown', onKeydown)
  onScroll() // in case the page loads already scrolled
})

onUnmounted(() => {
  window.removeEventListener('scroll', onScroll)
  document.removeEventListener('keydown', onKeydown)
})

// Navigating away should always leave the mobile menu closed.
// watch() = "when this reactive thing changes, run this code".
watch(() => route.fullPath, closeMenu)
</script>

<template>
  <header class="navbar" :class="{ 'navbar--scrolled': isScrolled }">
    <div class="container navbar__inner">
      <router-link to="/" class="navbar__logo" aria-label="NOVA TECH home">
        <span class="navbar__logo-icon" aria-hidden="true">N</span>
        <span class="navbar__logo-text">NOVA<span>TECH</span></span>
      </router-link>

      <!--
        The .open class here is REQUIRED. responsive.css line 157 only reveals
        the mobile menu for `.navbar__menu.open` - the button's own .active
        class is only the X animation. Without this the hamburger does nothing.
      -->
      <nav
        class="navbar__menu"
        :class="{ open: isMenuOpen }"
        aria-label="Main navigation"
      >
        <ul class="navbar__list">
          <!--
            router-link renders a real <a href="/about"> but navigates without a
            full page reload. The active state is bound with :class below.
          -->
          <li v-for="link in navLinks" :key="link.to">
            <router-link
              :to="link.to"
              class="navbar__link"
              :class="{ active: route.path === link.to }"
              @click="closeMenu"
            >
              {{ link.label }}
            </router-link>
          </li>
        </ul>
      </nav>

      <div class="navbar__actions">
        <ThemeToggle />

        <!--
          v-if / v-else swaps the whole element. The original project used
          textContent + href + class swapping on the SAME node, which only
          worked on 3 of 9 pages. Two elements is clearer and always correct.
        -->
        <router-link
          v-if="isLoggedIn"
          to="/dashboard"
          class="btn btn--primary btn--sm"
        >
          Dashboard
        </router-link>
        <router-link
          v-else
          to="/login"
          class="btn btn--ghost btn--sm"
        >
          Login
        </router-link>

        <button
          class="navbar__toggle"
          :class="{ active: isMenuOpen }"
          aria-label="Toggle navigation menu"
          :aria-expanded="isMenuOpen ? 'true' : 'false'"
          @click="isMenuOpen = !isMenuOpen"
        >
          <!--
            IMPORTANT: the CSS animates these with :nth-child(1|2|3), so these
            three spans must stay the first three element children. Do not wrap
            them or add any other element before them.
          -->
          <span class="navbar__toggle-bar"></span>
          <span class="navbar__toggle-bar"></span>
          <span class="navbar__toggle-bar"></span>
        </button>
      </div>
    </div>
  </header>
</template>
