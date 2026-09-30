<script setup>
import SectionHead from '@/components/ui/SectionHead.vue'
import JobCard from '@/components/careers/JobCard.vue'
import { jobs } from '@/data/careers'
</script>

<template>
  <section class="jobs section-pad">
    <div class="container">
      <SectionHead tag="Open Roles" title="We Are Hiring" />

      <!--
        The original hardcoded "Currently 7 open positions" in the description.
        Deriving it from jobs.length means the sentence can never drift out of
        sync with the actual list - add a role and it updates itself.
      -->
      <p class="section-head__desc">
        Currently {{ jobs.length }} open positions across engineering, design, AI, security and
        delivery.
      </p>

      <div class="jobs-grid">
        <!--
          77 lines of hand-written HTML became 6. The seven cards differed only
          in five data fields, so there was never a reason to write them out.
        -->
        <JobCard v-for="job in jobs" :key="job.id" :job="job" />
      </div>
    </div>
  </section>
</template>
