<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import NavBar from '@/components/layout/NavBar.vue'
import SiteFooter from '@/components/layout/SiteFooter.vue'
import BaseModal from '@/components/ui/BaseModal.vue'

const route = useRoute()

// The dashboard had its own sidebar shell and NO navbar/footer in the original.
// Rather than nesting a second shell, we mark the route with
// meta: { chrome: false } and conditionally skip the shared parts here.
const showChrome = computed(() => route.meta.chrome !== false)
</script>

<template>
  <NavBar v-if="showChrome" />

  <!--
    <router-view /> is the placeholder for "whichever view matches the current
    URL". It uses the default slot, so each view must have exactly ONE root
    element.
  -->
  <router-view />

  <SiteFooter v-if="showChrome" />

  <!--
    ONE modal for the entire app, always mounted, mounted to <body> by
    Teleport. In the original project main.js created this container on demand
    and threw it away; here it simply always exists and toggles a class.
    Because it lives here, every page can call useModal().openModal(...)
    without rendering anything.
  -->
  <BaseModal />
</template>
