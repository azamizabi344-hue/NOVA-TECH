<script setup>
import PageHero from '@/components/layout/PageHero.vue'
import CtaStrip from '@/components/layout/CtaStrip.vue'
import CultureSection from '@/components/careers/CultureSection.vue'
import JobsSection from '@/components/careers/JobsSection.vue'
</script>

<template>
  <PageHero
    title="Careers At NOVA TECH"
    crumb="Careers"
    desc="Join a team that ships meaningful products, grows together and has a little fun along the way."
  />

  <CultureSection />
  <JobsSection />

  <CtaStrip
    title="Don't See Your Role?"
    desc="We always love meeting talented people. Send us your story."
    cta="Get In Touch"
    to="/contact"
  />
</template>
