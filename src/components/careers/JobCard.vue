<script setup>
defineProps({
  job: { type: Object, required: true },
})
</script>

<template>
  <article v-reveal class="job-card">
    <div class="job-card__top">
      <span class="job-card__dept">{{ job.department }}</span>
      <span class="job-card__type">{{ job.type }}</span>
    </div>

    <h3 class="job-card__title">{{ job.title }}</h3>

    <p class="job-card__meta">{{ job.meta }}</p>

    <p class="job-card__desc">{{ job.description }}</p>

    <!--
      The original pointed all 7 "Apply Now" links at contact.html, with no
      indication of which role applied for. That is faithfully reproduced -
      see the note in the data file.
    -->
    <router-link to="/contact" class="btn btn--outline btn--sm">Apply Now</router-link>
  </article>
</template>
