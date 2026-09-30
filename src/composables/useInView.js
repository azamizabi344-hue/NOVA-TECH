/**
 * A single shared IntersectionObserver.
 *
 * main.js in the original project did exactly this (createRevealObserver) but
 * stored the observer on `window`. Keeping ONE instance is the important part:
 * creating an observer per component would be wasteful, and the original
 * counters observer and reveal observer could have been one.
 */

let observer = null
const callbacks = new WeakMap()

function getObserver() {
  if (observer) return observer

  observer = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (!entry.isIntersecting) continue

        // One-shot: stop watching as soon as it has been seen.
        observer.unobserve(entry.target)

        const callback = callbacks.get(entry.target)
        if (callback) {
          callbacks.delete(entry.target)
          callback(entry.target)
        }
      }
    },
    { threshold: 0.15 },
  )

  return observer
}

/**
 * Runs `callback(element)` the first time `element` scrolls into view.
 * @param {Element} el
 * @param {(el: Element) => void} callback
 */
export function onceVisible(el, callback) {
  if (!el) return
  callbacks.set(el, callback)
  getObserver().observe(el)
}

/** Clean up - call from onUnmounted so observers never hold dead nodes. */
export function stopWatching(el) {
  if (!el) return
  callbacks.delete(el)
  if (observer) observer.unobserve(el)
}
