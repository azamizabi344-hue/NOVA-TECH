<script setup>
import PageHero from '@/components/layout/PageHero.vue'
import CtaStrip from '@/components/layout/CtaStrip.vue'
import StatsGrid from '@/components/ui/StatsGrid.vue'
import StorySection from '@/components/about/StorySection.vue'
import MissionSection from '@/components/about/MissionSection.vue'
import ValuesSection from '@/components/about/ValuesSection.vue'
import TimelineSection from '@/components/about/TimelineSection.vue'
import LeadershipSection from '@/components/about/LeadershipSection.vue'
import { aboutStats } from '@/data/stats'
</script>

<template>
  <PageHero
    title="About NOVA TECH"
    crumb="About"
    desc="A full-service technology company built on curiosity, craft and trust."
  />

  <StorySection />
  <MissionSection />
  <ValuesSection />

  <!--
    The SAME StatsGrid component the homepage uses, with different data.
    The original duplicated this 16-line block by hand into about.html.
  -->
  <StatsGrid :stats="aboutStats" />

  <TimelineSection />
  <LeadershipSection />

  <CtaStrip
    title="Want To Build Something Great Together?"
    desc="Let's talk about your project — no strings attached."
    cta="Get In Touch"
    to="/contact"
  />
</template>
