import { ref, computed } from 'vue'
import { readSession, writeSession, removeSession } from '@/utils/storage'

// The two demo accounts from the original auth.js.
export const demoUsers = [
  {
    name: 'Admin',
    email: 'admin@novatech.com',
    password: 'admin123',
    role: 'Administrator',
  },
  {
    name: 'Jane Cooper',
    email: 'jane@novatech.com',
    password: 'jane123',
    role: 'Project Manager',
  },
]

export const SESSION_KEY = 'novatech_session'
export const REMEMBER_KEY = 'novatech_remembered_email'

// Module-scope ref = ONE shared piece of state for the whole app.
// This is the Vue replacement for `window.auth` in the original project.
const currentUser = ref(readSession(SESSION_KEY)?.user ?? null)

/**
 * The remembered email, also module-scope so every caller shares one value -
 * the same reason currentUser lives out here. If this were created inside
 * useAuth(), each component would get its own ref and they would drift apart.
 */
const rememberedEmail = ref(readSession(REMEMBER_KEY) ?? '')

/**
 * Checks the typed credentials against the demo list.
 * Email match is case-insensitive, exactly like the original.
 */
export function findDemoUser(email, password) {
  return demoUsers.find(
    (user) =>
      user.email.toLowerCase() === String(email).trim().toLowerCase() &&
      user.password === password,
  )
}

export function useAuth() {
  const isLoggedIn = computed(() => currentUser.value !== null)

  /** Creates the session. Called by the login form on success. */
  function startSession(user) {
    const session = {
      // Note: the password is deliberately NOT stored - only these 3 fields.
      user: { name: user.name, email: user.email, role: user.role },
      loginAt: new Date().toISOString(),
    }
    writeSession(SESSION_KEY, session)
    currentUser.value = session.user
    return session.user
  }

  /**
   * Clears the session. Navigation is left to the caller (needs the router).
   *
   * NOTE: this deliberately does NOT clear REMEMBER_KEY. The original's
   * logout() only removed SESSION_KEY, and that is the correct behaviour - if
   * someone ticked "Remember me", their email should still be prefilled the
   * next time they visit /login. Forgetting it here would silently break the
   * feature the user explicitly asked for.
   */
  function logout() {
    removeSession(SESSION_KEY)
    currentUser.value = null
  }

  function rememberEmail(email) {
    writeSession(REMEMBER_KEY, email)
    rememberedEmail.value = email
  }

  function forgetEmail() {
    removeSession(REMEMBER_KEY)
    rememberedEmail.value = ''
  }

  return {
    currentUser,
    isLoggedIn,
    startSession,
    logout,
    rememberedEmail,
    rememberEmail,
    forgetEmail,
  }
}
