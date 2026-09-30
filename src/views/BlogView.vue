<script setup>
import PageHero from '@/components/layout/PageHero.vue'
import CtaStrip from '@/components/layout/CtaStrip.vue'
import BlogExplorer from '@/components/explorers/BlogExplorer.vue'
</script>

<template>
  <!--
    "Blog & Insights" is a static string, so writing it as-is in the template
    is clearer than routing it through the data file. Vue escapes the & for us.
  -->
  <PageHero
    title="Blog & Insights"
    crumb="Blog"
    desc="Ideas, lessons and behind-the-scenes stories from our engineering, design, AI and security teams."
  />

  <BlogExplorer />

  <CtaStrip
    title="Want To Work With Us?"
    desc="Explore our open roles or start a conversation."
    cta="See Careers"
    to="/careers"
  />
</template>
