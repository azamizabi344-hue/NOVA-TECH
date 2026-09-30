<script setup>
import SectionHead from '@/components/ui/SectionHead.vue'
import { aboutTeaser } from '@/data/home'
</script>

<template>
  <section class="about section-pad">
    <div class="container about__inner">
      <div class="about__visual" aria-hidden="true">
        <div class="about__img-placeholder">
          <span>{{ aboutTeaser.imageLabel }}</span>
        </div>
      </div>

      <div class="about__content">
        <SectionHead :tag="aboutTeaser.tag" :title="aboutTeaser.title" />

        <p class="about__story">{{ aboutTeaser.story }}</p>

        <div class="about__values">
          <!-- Was 3 hardcoded <div class="about__value"> blocks -->
          <div v-for="value in aboutTeaser.values" :key="value.id" class="about__value">
            <h4>{{ value.title }}</h4>
            <p>{{ value.text }}</p>
          </div>
        </div>

        <router-link to="/about" class="btn btn--primary">More About Us</router-link>
      </div>
    </div>
  </section>
</template>
