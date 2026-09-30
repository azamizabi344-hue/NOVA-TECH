/**
 * Thin, safe wrappers around the browser storage APIs.
 *
 * Why not call localStorage directly? Because it throws in private-browsing
 * mode and when a user has cookies/storage blocked. Your original project
 * wrapped every access in try/catch - these helpers do that once, for
 * everyone, so no component needs to.
 *
 * The names are identical to the browser's, which makes the intent obvious:
 * readStorage / writeStorage / removeStorage
 */

function canUseStorage(storage) {
  try {
    const probe = '__nova_probe__'
    storage.setItem(probe, '1')
    storage.removeItem(probe)
    return true
  } catch (error) {
    return false
  }
}

const localOK = canUseStorage(window.localStorage)
const sessionOK = canUseStorage(window.sessionStorage)

export function readStorage(key, fallback = null) {
  try {
    const raw = localStorage.getItem(key)
    return raw === null ? fallback : JSON.parse(raw)
  } catch (error) {
    console.error(`Could not read "${key}" from localStorage:`, error)
    return fallback
  }
}

export function writeStorage(key, value) {
  try {
    localStorage.setItem(key, JSON.stringify(value))
    return true
  } catch (error) {
    console.error(`Could not write "${key}" to localStorage:`, error)
    return false
  }
}

export function removeStorage(key) {
  try {
    localStorage.removeItem(key)
    return true
  } catch (error) {
    console.error(`Could not remove "${key}" from localStorage:`, error)
    return false
  }
}

export function readSession(key, fallback = null) {
  if (!sessionOK) return fallback
  try {
    const raw = sessionStorage.getItem(key)
    return raw === null ? fallback : JSON.parse(raw)
  } catch (error) {
    console.error(`Could not read "${key}" from sessionStorage:`, error)
    return fallback
  }
}

export function writeSession(key, value) {
  if (!sessionOK) return false
  try {
    sessionStorage.setItem(key, JSON.stringify(value))
    return true
  } catch (error) {
    console.error(`Could not write "${key}" to sessionStorage:`, error)
    return false
  }
}

export function removeSession(key) {
  if (!sessionOK) return false
  try {
    sessionStorage.removeItem(key)
    return true
  } catch (error) {
    console.error(`Could not remove "${key}" from sessionStorage:`, error)
    return false
  }
}

/** False when the browser refuses to store anything (private mode, blocked). */
export const storageAvailable = localOK || sessionOK
