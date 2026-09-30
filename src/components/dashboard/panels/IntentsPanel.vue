<script setup>
import Badge from '@/components/ui/Badge.vue'
import { intents } from '@/data/dashboard'

/**
 * IntentsPanel - the six chatbot intents.
 *
 * Replaces renderIntentsPanel(). The original derived the badge colour with a
 * ternary - 'active' ? badge--active : badge--pending - and that exact rule is
 * kept here, just expressed as the `tone` prop.
 */
</script>

<template>
  <div class="panel__header">
    <div>
      <h2 class="panel__title">Intents</h2>
      <p class="panel__sub">Chatbot intents configured in the AI assistant.</p>
    </div>
  </div>

  <div class="dash-intents">
    <div v-for="intent in intents" :key="intent.name" class="dash-intents__item">
      <span class="dash-intents__icon" aria-hidden="true">{{ intent.icon }}</span>
      <div>
        <h4>{{ intent.name }}</h4>
        <p>{{ intent.desc }}</p>
        <Badge :label="intent.status" :tone="intent.status === 'active' ? 'active' : 'pending'" />
      </div>
    </div>
  </div>
</template>
