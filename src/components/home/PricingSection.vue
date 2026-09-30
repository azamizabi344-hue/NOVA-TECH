<script setup>
import SectionHead from '@/components/ui/SectionHead.vue'
import { pricingPlans } from '@/data/home'
</script>

<template>
  <section class="pricing section-pad" id="pricing">
    <div class="container">
      <SectionHead
        tag="Pricing"
        title="Simple, Transparent Plans"
        desc="Choose the plan that fits your stage. Scale up whenever you need more."
      />

      <div class="pricing__grid">
        <!--
          One article for all three plans. The differences are data:
            featured -> extra .pricing-card--featured class
            badge    -> the "Most Popular" pill, omitted when empty
            period   -> "/project", empty for the Enterprise "Custom" price
        -->
        <article
          v-for="(plan, index) in pricingPlans"
          :key="plan.id"
          v-reveal="index + 1"
          class="pricing-card"
          :class="{ 'pricing-card--featured': plan.featured }"
        >
          <span v-if="plan.badge" class="pricing-card__badge">{{ plan.badge }}</span>

          <h3 class="pricing-card__plan">{{ plan.plan }}</h3>

          <!-- currency / price / period are separate spans for the typography -->
          <p class="pricing-card__price">
            <span class="pricing-card__currency">{{ plan.currency }}</span>{{ plan.price
            }}<span v-if="plan.period" class="pricing-card__period">{{ plan.period }}</span>
          </p>

          <ul class="pricing-card__features">
            <li v-for="feature in plan.features" :key="feature">{{ feature }}</li>
          </ul>

          <!-- contact.html becomes the /contact route -->
          <router-link :to="'/contact'" :class="plan.buttonClass">{{ plan.cta }}</router-link>
        </article>
      </div>
    </div>
  </section>
</template>
