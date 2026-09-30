/**
 * Small string helpers shared across the app.
 *
 * NOTE ON escapeHtml()
 * ---------------------
 * The original project built HTML with template strings and therefore had to
 * escape every value by hand (window.escapeHtml). Vue templates do this for
 * you automatically: writing {{ userInput }} can never inject markup. The only
 * place that would need manual escaping is v-html / innerHTML, and we avoid
 * both by turning raw SVG strings into real components.
 *
 * capitalize() was copy-pasted into 5 separate JS files in the original
 * project. Here it lives in exactly one place.
 */

export function capitalize(value) {
  if (!value) return ''
  return String(value).charAt(0).toUpperCase() + String(value).slice(1)
}

export function isValidEmail(email) {
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailPattern.test(email)
}

export function isValidPhone(phone) {
  const phonePattern = /^[+]?[\d\s\-()]{7,15}$/
  return phonePattern.test(phone)
}

/** Case-insensitive "does this haystack contain the needle" over a list. */
export function matchesTerm(haystack, needle) {
  return haystack.some((item) => String(item).toLowerCase().includes(needle))
}

/** Formats an ISO date string the way the dashboard's message list did. */
export function formatDate(value) {
  try {
    return new Date(value).toLocaleDateString(undefined, {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    })
  } catch (error) {
    return 'Unknown date'
  }
}
