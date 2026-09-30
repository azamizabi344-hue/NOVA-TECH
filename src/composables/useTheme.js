import { ref, watch } from 'vue'

const STORAGE_KEY = 'nova-theme'

// Read the CURRENT state straight off the <html> element.
// The inline script in index.html already applied the saved theme before Vue
// even loaded, so this reads the truth and never causes a flash of the wrong
// theme.
const isDark = ref(document.documentElement.getAttribute('data-theme') === 'dark')

// A watcher is "when this value changes, do this". Because `isDark` is the only
// piece of state, the DOM attribute and localStorage can never drift apart.
watch(isDark, (dark) => {
  const root = document.documentElement
  if (dark) {
    root.setAttribute('data-theme', 'dark')
  } else {
    root.removeAttribute('data-theme')
  }
  try {
    localStorage.setItem(STORAGE_KEY, dark ? 'dark' : 'light')
  } catch (error) {
    // Private browsing / storage unavailable - ignore.
  }
})

// The ref lives at module scope (outside the function) on purpose: every
// component that calls useTheme() gets the SAME reactive value.
export function useTheme() {
  function toggleTheme() {
    isDark.value = !isDark.value
  }

  return { isDark, toggleTheme }
}
