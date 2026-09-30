<script setup>
/**
 * ResultCount - the "N projects found" line.
 *
 * The original wrote the string by hand in 4 places:
 *   countLabel.textContent = `${filtered.length} project${filtered.length === 1 ? '' : 's'} found`
 * A small component removes that duplication.
 */

const props = defineProps({
  count: { type: Number, required: true },
  // singular noun, e.g. 'project'. The plural is formed by adding "s",
  // which is exactly right for project/service/article but not for
  // "team member" - so allow an explicit plural too.
  singular: { type: String, required: true },
  plural: { type: String, default: '' },
})

function label() {
  const word = props.count === 1 ? props.singular : (props.plural || `${props.singular}s`)
  return `${props.count} ${word} found`
}
</script>

<template>
  <p class="services-count" aria-live="polite">{{ label() }}</p>
</template>
