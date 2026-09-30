<script setup>
import SectionHead from '@/components/ui/SectionHead.vue'
import { testimonials } from '@/data/home'
</script>

<template>
  <section class="testimonials section-pad">
    <div class="container">
      <SectionHead tag="Client Feedback" title="What Our Clients Say" />

      <div class="testimonials__grid">
        <!--
          The stagger (reveal-delay-1/2/3) came from main.js looping over
          .testimonials__grid children, so it is preserved here per card.
        -->
        <article
          v-for="(item, index) in testimonials"
          :key="item.id"
          v-reveal="index < 3 ? index + 1 : 0"
          class="testimonial-card"
        >
          <!--
            stars is a NUMBER in the data. The original hand-wrote four &#9733;
            and one &#9734; for the third card; now the count decides which
            glyph is used and the aria-label is built from the same number.
          -->
          <div class="testimonial-card__stars" :aria-label="`${item.stars} out of 5 stars`">
            <span v-for="n in item.stars" :key="`full-${n}`">&#9733;</span>
            <span v-for="n in 5 - item.stars" :key="`empty-${n}`">&#9734;</span>
          </div>

          <p class="testimonial-card__quote">{{ item.quote }}</p>

          <div class="testimonial-card__author">
            <span class="testimonial-card__avatar">{{ item.initials }}</span>
            <div>
              <h4>{{ item.author }}</h4>
              <span>{{ item.role }}</span>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>
</template>
