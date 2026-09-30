<script setup>
import PageHero from '@/components/layout/PageHero.vue'
import CtaStrip from '@/components/layout/CtaStrip.vue'
import TeamExplorer from '@/components/explorers/TeamExplorer.vue'
</script>

<template>
  <PageHero
    title="Meet The Team"
    crumb="Team"
    desc="Fifty-plus curious engineers, designers and specialists — these are the people behind every NOVA TECH product."
  />

  <TeamExplorer />

  <CtaStrip
    title="Work With This Team"
    desc="We are always happy to talk about your next project."
    cta="Contact Us"
    to="/contact"
  />
</template>
