<script setup>
import PageHero from '@/components/layout/PageHero.vue'
import CtaStrip from '@/components/layout/CtaStrip.vue'
import ProjectsExplorer from '@/components/explorers/ProjectsExplorer.vue'
</script>

<template>
  <!--
    The whole page is now 3 components. Compare with projects.html, which had
    58 lines of navbar + 12 lines of hero + 42 lines of toolbar/filter/grid +
    7 lines of CTA + 47 lines of footer, all duplicated across 8 files.
  -->
  <PageHero
    title="Our Projects"
    desc="Real products we have designed, built and shipped for clients across web, mobile, AI and cyber security."
  />

  <ProjectsExplorer />

  <CtaStrip
    title="Have A Project In Mind?"
    desc="Let's make it real. Tell us what you want to build."
    cta="Start A Project"
    to="/contact"
  />
</template>
