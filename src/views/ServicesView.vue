<script setup>
import PageHero from '@/components/layout/PageHero.vue'
import CtaStrip from '@/components/layout/CtaStrip.vue'
import ServicesExplorer from '@/components/explorers/ServicesExplorer.vue'
</script>

<template>
  <PageHero
    title="Our Services"
    desc="From first wireframe to global launch — everything you need to build, secure and scale modern digital products."
  />

  <ServicesExplorer />

  <CtaStrip
    title="Need A Custom Solution?"
    desc="Tell us about your project and we'll build the right plan for you."
    cta="Request A Quote"
    to="/contact"
  />
</template>
