<script setup>
import Counter from '@/components/ui/Counter.vue'
import { heroStats } from '@/data/stats'
</script>

<template>
  <section class="hero">
    <div class="hero__background" aria-hidden="true"></div>

    <div class="container hero__inner">
      <div class="hero__content">
        <span class="hero__badge">✦ Trusted by 150+ companies worldwide</span>

        <h1 class="hero__title">
          Building the <span class="text-gradient">Future</span> of Technology
        </h1>

        <p class="hero__subtitle">
          NOVA TECH designs and ships cutting-edge web platforms, mobile apps, AI
          systems and secure cloud solutions that help your business grow faster
          and smarter.
        </p>

        <div class="hero__buttons">
          <router-link to="/contact" class="btn btn--primary">Get Started</router-link>
          <router-link to="/projects" class="btn btn--outline">View Projects</router-link>
        </div>

        <!--
          v-for over the 3 stats. This used to be three copy-pasted
          <div class="hero__stat"> blocks, each with its own
          <span class="hero__stat-number counter" data-target="...">0</span>.

          Note `class="hero__stat-number"` on <Counter>. Vue's FALLTHROUGH
          ATTRIBUTES merge any extra class onto a child component's single
          root element, so <Counter class="hero__stat-number"> renders
          <span class="counter hero__stat-number" data-target="150">0</span> -
          byte-for-byte the original markup, without Counter needing to know
          anything about the hero.
        -->
        <div class="hero__stats">
          <div v-for="stat in heroStats" :key="stat.id" class="hero__stat">
            <Counter :target="stat.value" class="hero__stat-number" />
            <span class="hero__stat-suffix">{{ stat.suffix }}</span>
            <span class="hero__stat-label">{{ stat.label }}</span>
          </div>
        </div>
      </div>

      <!-- Decorative animated graphic. Purely CSS - no JS involved. -->
      <div class="hero__visual" aria-hidden="true">
        <div class="tech-visual">
          <div class="tech-visual__grid"></div>

          <div class="tech-visual__core">
            <span class="tech-visual__icon">&#9883;</span>
            <span class="tech-visual__icon tech-visual__icon--2">&#9670;</span>
            <span class="tech-visual__icon tech-visual__icon--3">&#9679;</span>
            <span class="tech-visual__icon tech-visual__icon--4">&#10022;</span>
            <span class="tech-visual__ring tech-visual__ring--1"></span>
            <span class="tech-visual__ring tech-visual__ring--2"></span>
          </div>

          <div class="tech-visual__card tech-visual__card--1">AI</div>
          <div class="tech-visual__card tech-visual__card--2">WEB</div>
          <div class="tech-visual__card tech-visual__card--3">CLOUD</div>
        </div>
      </div>
    </div>
  </section>
</template>
