<script setup>
import SectionHead from '@/components/ui/SectionHead.vue'
import { missionCards } from '@/data/about'
</script>

<template>
  <section class="mission section-pad">
    <div class="container">
      <SectionHead tag="Why We Exist" title="Mission & Vision" />

      <div class="mission-grid">
        <!--
          The original wrote three near-identical <article class="mission-card">
          blocks. The only differences were the emoji, the title and the text -
          exactly the three fields in the data.
        -->
        <article
          v-for="(card, index) in missionCards"
          :key="card.id"
          v-reveal="index + 1"
          class="mission-card"
        >
          <div class="mission-card__icon" aria-hidden="true">{{ card.icon }}</div>

          <h3 class="mission-card__title">{{ card.title }}</h3>

          <p>{{ card.text }}</p>
        </article>
      </div>
    </div>
  </section>
</template>
