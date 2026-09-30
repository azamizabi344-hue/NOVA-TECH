<script setup>
import { capitalize } from '@/utils/strings'

/**
 * Badge - the small status pill used all over the dashboard.
 *
 * The original wrote the same markup by hand in four different template
 * strings:
 *
 *   <span class="badge badge--${project.status}">${capitalize(project.status)}</span>
 *   <span class="badge ${intent.status === 'active' ? 'badge--active' : 'badge--pending'}">
 *   <span class="badge ${user.status === 'active' ? 'badge--active' : 'badge--pending'}">
 *
 * The class name is data-driven here, so `badge--` + tone cannot be mistyped,
 * and the label is passed separately from the colour. That matters because the
 * Users panel needs a 'pending' ORANGE dot for an 'invited' user - same colour,
 * different text.
 */
defineProps({
  /** The visible text, e.g. 'active', 'Invited', 'Done'. */
  label: { type: String, required: true },
  /** Which CSS modifier to use: active | pending | done. */
  tone: { type: String, required: true },
})
</script>

<template>
  <span class="badge" :class="`badge--${tone}`">{{ capitalize(label) }}</span>
</template>
