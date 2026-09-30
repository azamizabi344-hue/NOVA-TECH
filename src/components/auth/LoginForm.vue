<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuth, findDemoUser } from '@/composables/useAuth'

/**
 * LoginForm - the demo sign-in form.
 *
 * Replaces initLoginForm() from the original auth.js. Two things changed
 * structurally:
 *
 * 1. The "show password" toggle is just `:type`. The original added a
 *    `change` listener that assigned `passwordInput.type = 'text'`. Now the
 *    checkbox is bound to a ref and the type is a bound expression - there is
 *    no listener to forget.
 *
 * 2. Navigation uses the router instead of `window.location.href`, so this is
 *    a client-side route change. The session itself lives in sessionStorage,
 *    exactly as before, so a hard refresh on /dashboard still works.
 */
const router = useRouter()
const route = useRoute()
const { startSession, rememberedEmail, rememberEmail, forgetEmail } = useAuth()

const email = ref('')
const password = ref('')
const rememberMe = ref(false)
const showPassword = ref(false)

const alertMessage = ref('')
const emailError = ref('')
const passwordError = ref('')

// Prefill from "Remember me". The original read sessionStorage directly on
// page load; the ref is already hydrated by the composable at module load.
onMounted(() => {
  if (rememberedEmail.value) {
    email.value = rememberedEmail.value
    rememberMe.value = true
  }
})

function onSubmit() {
  const trimmedEmail = email.value.trim()

  // Both empty -> show the shared alert AND mark the empty fields.
  if (trimmedEmail === '' || password.value === '') {
    alertMessage.value = 'Please enter both email and password.'
    emailError.value = trimmedEmail === '' ? 'Email is required.' : ''
    passwordError.value = password.value === '' ? 'Password is required.' : ''
    return
  }

  const user = findDemoUser(trimmedEmail, password.value)

  if (!user) {
    alertMessage.value = 'Invalid email or password. Please try again.'
    return
  }

  // Success: clear the errors and create the session.
  alertMessage.value = ''
  emailError.value = ''
  passwordError.value = ''

  startSession(user)

  // "Remember me" is stored in sessionStorage, not localStorage, in the
  // original - the email is prefilled for this browser session only.
  if (rememberMe.value) rememberEmail(trimmedEmail)
  else forgetEmail()

  router.push(destination.value)
}

/**
 * Where to go after a successful sign-in.
 *
 * The router guard adds ?redirect=/dashboard when it bounces a visitor away from
 * a protected page, so honouring it means a deep link survives the detour.
 *
 * The path is validated rather than used raw: without this check,
 * /login?redirect=//evil.example.com would be treated as a protocol-relative
 * URL and hand the visitor to another site after they sign in.
 */
const destination = computed(() => {
  const redirect = route.query.redirect
  if (typeof redirect === 'string' && redirect.startsWith('/') && !redirect.startsWith('//')) {
    return redirect
  }
  return '/dashboard'
})
</script>

<template>
  <section class="auth">
    <div class="auth__box">
      <h1 class="auth__title">Welcome Back</h1>
      <p class="auth__sub">Log in to access your NOVA TECH dashboard.</p>

      <!-- v-show matches the original's classList.add('show') behaviour -->
      <div v-show="alertMessage" class="auth__alert" role="alert">{{ alertMessage }}</div>

      <form novalidate @submit.prevent="onSubmit">
        <div class="form__group" :class="{ error: emailError }">
          <label class="form__label" for="login-email">
            Email Address <span class="form__required">*</span>
          </label>
          <input
            id="login-email"
            v-model="email"
            type="email"
            class="form__input"
            placeholder="admin@novatech.com"
            autocomplete="email"
            @input="emailError = ''"
          >
          <p class="form__error">{{ emailError }}</p>
        </div>

        <div class="form__group" :class="{ error: passwordError }">
          <label class="form__label" for="login-password">
            Password <span class="form__required">*</span>
          </label>
          <!--
            :type is bound to the checkbox. The original did this with a change
            listener that assigned .type imperatively.
          -->
          <input
            id="login-password"
            v-model="password"
            :type="showPassword ? 'text' : 'password'"
            class="form__input"
            placeholder="Enter your password"
            autocomplete="current-password"
            @input="passwordError = ''"
          >
          <p class="form__error">{{ passwordError }}</p>
        </div>

        <div class="form__group">
          <label class="checkbox-group">
            <input v-model="rememberMe" type="checkbox" id="remember-me">
            Remember me
          </label>
        </div>

        <button type="submit" class="btn btn--primary btn--full">Log In</button>

        <label class="checkbox-group auth__show-password">
          <input v-model="showPassword" type="checkbox" id="show-password">
          Show password
        </label>
      </form>

      <div class="auth__demo">
        <strong>Demo credentials</strong> (client-side only — nothing is sent to a server):<br>
        Email: <code>admin@novatech.com</code><br>
        Password: <code>admin123</code>
      </div>
    </div>
  </section>
</template>
