<script setup>
import StatCard from '@/components/ui/StatCard.vue'
import { homeStats } from '@/data/stats'

/**
 * StatsGrid - the four animated statistics.
 *
 * The homepage and the about page render the SAME markup with the SAME classes
 * and different numbers:
 *   index.html  -> 150+ projects, 50+ team, 30+ countries, 98% satisfaction
 *   about.html  -> 150+ projects, 50+ team, 30+ countries, 6 years
 *
 * The original copy-pasted the whole 16-line block into both files. Now the
 * markup lives here once and the data decides the numbers.
 */
defineProps({
  // Defaults to the homepage set, so <StatsGrid /> on the home page needs no
  // props at all. The about page passes :stats="aboutStats".
  stats: { type: Array, default: () => homeStats },
})
</script>

<template>
  <section class="stats section-pad">
    <div class="container">
      <div class="stats__grid">
        <!--
          v-for + :key over the stats. The index supplies the stagger delay
          (1, 2, 3 then 0) exactly like the original:
            if (i < 3) group.children[i].classList.add('reveal-delay-' + (i+1))
          The (index + 1) makes the first card delay 1, second delay 2, etc.
        -->
        <StatCard
          v-for="(stat, index) in stats"
          :key="stat.id"
          :value="stat.value"
          :suffix="stat.suffix"
          :label="stat.label"
          :delay="index < 3 ? index + 1 : 0"
        />
      </div>
    </div>
  </section>
</template>
