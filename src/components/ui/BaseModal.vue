<script setup>
import { ref, watch, nextTick, onMounted, onUnmounted } from 'vue'
import { useModal } from '@/composables/useModal'

const { isOpen, activeComponent, activeProps, closeModal } = useModal()

// The close button. A template ref instead of document.querySelector, so
// Vue gives us the element - no global lookup that could match the wrong node.
const closeButton = ref(null)

// The element that had focus before the modal opened, so focus can be given
// back when it closes. The original never did this; without it, closing a modal
// drops keyboard users back at the top of the document.
let previouslyFocused = null

// --- Body scroll lock (was: document.body.style.overflow in main.js) --------
// A watcher guarantees it is always undone, even if some other code calls
// closeModal() directly.
watch(isOpen, (open) => {
  document.body.style.overflow = open ? 'hidden' : ''
})

// --- Focus management (was: modal.querySelector('.modal__close').focus()) ----
// nextTick is required: the modal content is rendered by <component :is>, so on
// the tick where isOpen flips the button exists but the inner content may not
// be mounted yet.
watch(isOpen, async (open) => {
  if (open) {
    previouslyFocused = document.activeElement
    await nextTick()
    closeButton.value?.focus()
  } else {
    // Only restore if the stored element is still in the document.
    if (previouslyFocused && document.contains(previouslyFocused)) {
      previouslyFocused.focus()
    }
    previouslyFocused = null
  }
})

// --- Escape key closes the modal -------------------------------------------
// main.js attached this to document only once; here it lives and dies with the
// component, so no listener can leak.
function onKeydown(event) {
  if (event.key === 'Escape' && isOpen.value) closeModal()
}

onMounted(() => document.addEventListener('keydown', onKeydown))
onUnmounted(() => document.removeEventListener('keydown', onKeydown))
</script>

<template>
  <!--
    <Teleport to="body"> moves this markup out of wherever the component sits
    and attaches it to <body> instead. The modal must be a child of body so it
    is never clipped by a section's overflow and always paints on top.
  -->
  <Teleport to="body">
    <!--
      The class binding drives the CSS transition exactly like the original
      classList.add('open') / classList.remove('open'). The markup is always
      present so the fade-out animation can play on close.
    -->
    <div
      class="modal"
      :class="{ open: isOpen }"
      role="dialog"
      aria-modal="true"
      @click.self="closeModal"
    >
      <div class="modal__box">
        <button ref="closeButton" class="modal__close" aria-label="Close" @click="closeModal">
          &times;
        </button>

        <div class="modal__content">
          <!--
            <component :is="..."> is Vue's dynamic component. It renders
            whichever component is currently stored in activeComponent, and
            v-bind hands it the matching props. This replaces
            modal.querySelector('.modal__content').innerHTML = html
          -->
          <component :is="activeComponent" v-bind="activeProps" />
        </div>
      </div>
    </div>
  </Teleport>
</template>
