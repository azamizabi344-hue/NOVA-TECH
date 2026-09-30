<script setup>
/**
 * PageHero - the banner at the top of every inner page.
 *
 * In the original this block was copy-pasted into 8 HTML files with only the
 * title, the description and the breadcrumb changing:
 *
 *   <section class="page-hero">
 *     <div class="container">
 *       <h1 class="page-hero__title">Our Projects</h1>
 *       <p class="page-hero__desc">Real products we have ...</p>
 *       <div class="breadcrumb">
 *         <a href="index.html">Home</a> / <span>Projects</span>
 *       </div>
 *     </div>
 *   </section>
 *
 * Now the structure is written once. index.html is the only page without one.
 */
defineProps({
  title: { type: String, required: true },
  desc: { type: String, default: '' },
  // The current page's name, shown as the non-link half of the breadcrumb.
  crumb: { type: String, default: '' },
})
</script>

<template>
  <section class="page-hero">
    <div class="container">
      <h1 class="page-hero__title">{{ title }}</h1>

      <p v-if="desc" class="page-hero__desc">{{ desc }}</p>

      <div class="breadcrumb">
        <!-- "Home" is always the first crumb on every page. -->
        <router-link to="/">Home</router-link> / <span>{{ crumb || title }}</span>
      </div>
    </div>
  </section>
</template>
