<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { onceVisible, stopWatching } from '@/composables/useInView'

// defineProps declares the component's inputs.
const props = defineProps({
  // was data-target="150" in the original HTML
  target: { type: Number, required: true },
  duration: { type: Number, default: 2000 },
})

const display = ref(0)     // the number currently shown
const element = ref(null)  // the real <span> DOM node
let frameId = null

function animate(node) {
  const startTime = performance.now()

  // requestAnimationFrame calls update() roughly 60 times per second.
  function update(currentTime) {
    const elapsed = currentTime - startTime
    // progress runs 0 -> 1 across the duration
    const progress = Math.min(elapsed / props.duration, 1)
    // Cubic ease-out: the exact formula from main.js, so the motion is
    // identical to the original.
    const eased = 1 - Math.pow(1 - progress, 3)

    display.value = Math.floor(eased * props.target)

    if (progress < 1) {
      frameId = requestAnimationFrame(update)
    } else {
      display.value = props.target // land exactly on the target
    }
  }

  frameId = requestAnimationFrame(update)
}

onMounted(() => {
  // Only start counting once the number is actually scrolled into view.
  onceVisible(element.value, (node) => animate(node))
})

onUnmounted(() => {
  // Without this, a pending animation frame would keep writing to a component
  // that no longer exists.
  if (frameId !== null) cancelAnimationFrame(frameId)
  stopWatching(element.value)
})
</script>

<template>
  <!--
    ref="element" links this <span> to the `element` ref declared above, which
    is how we get a real DOM node to hand to the observer.
  -->
  <span ref="element" class="counter" :data-target="target">{{ display }}</span>
</template>
