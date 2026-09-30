import { ref, shallowRef } from 'vue'

// ---------------------------------------------------------------------------
// ONE shared modal for the whole app.
//
// The original main.js built a container on the fly and then did
// content.innerHTML = html. Here the "content" is a COMPONENT plus its props,
// so there is no HTML string anywhere and escapeHtml() is never needed.
// ---------------------------------------------------------------------------

// isOpen lives at module scope, so every caller of useModal() shares it.
const isOpen = ref(false)

// shallowRef for the component: we never want Vue to make a component deeply
// reactive (that would wrap its internals in Proxies - wasteful and a bug farm).
const activeComponent = shallowRef(null)
const activeProps = ref({})

export function useModal() {
  /**
   * @param {object} component  the .vue file to render inside the modal
   * @param {object} props      props handed to that component
   */
  function openModal(component, props = {}) {
    activeComponent.value = component
    activeProps.value = props
    isOpen.value = true
  }

  function closeModal() {
    isOpen.value = false
  }

  return { isOpen, activeComponent, activeProps, openModal, closeModal }
}
