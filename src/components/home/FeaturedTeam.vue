<script setup>
import SectionHead from '@/components/ui/SectionHead.vue'
import TeamCard from '@/components/cards/TeamCard.vue'
import { featuredMembers } from '@/data/team'
</script>

<template>
  <section class="team section-pad">
    <div class="container">
      <SectionHead
        tag="Meet The Experts"
        title="Talented Team Behind The Scenes"
        desc="Our people are the heart of NOVA TECH — engineers, designers and strategists who care about quality."
      />

      <!--
        featuredMembers is already resolved in the data file (ids 1,4,5,6,8,10
        mapped to their objects). This section has NO filter bar - the original
        homepage team section was a fixed 6-card grid.
      -->
      <div class="team__grid">
        <TeamCard
          v-for="member in featuredMembers"
          :key="member.id"
          :member="member"
        />
      </div>

      <div class="projects__more">
        <router-link to="/team" class="btn btn--outline">Meet The Whole Team</router-link>
      </div>
    </div>
  </section>
</template>
