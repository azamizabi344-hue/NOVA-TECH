<script setup>
import SectionHead from '@/components/ui/SectionHead.vue'
import { aboutStory } from '@/data/about'
</script>

<template>
  <section class="about-page section-pad">
    <div class="container about-page__inner">
      <!--
        A text placeholder rather than an <img>, because the project ships no
        images. aria-hidden because it carries no information.
      -->
      <div class="about-page__visual" aria-hidden="true">{{ aboutStory.visualLabel }}</div>

      <div class="about-page__text">
        <!--
          SectionHead renders .section-head__tag + .section-head__title, which
          is exactly the two elements the original had here.
        -->
        <SectionHead :tag="aboutStory.tag" :title="aboutStory.title" />

        <!-- v-for over the two paragraphs instead of two hand-wrapped <p>s -->
        <p v-for="(text, index) in aboutStory.paragraphs" :key="index">{{ text }}</p>
      </div>
    </div>
  </section>
</template>
