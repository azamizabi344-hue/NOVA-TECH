<script setup>
import { ref, onBeforeUnmount } from 'vue'
import { isValidEmail } from '@/utils/strings'
import { readStorage, writeStorage } from '@/utils/storage'

const STORAGE_KEY = 'novatech_newsletter'

const email = ref('')
const message = ref('')
const type = ref('')
const visible = ref(false)

/**
 * The original cleared its 4-second timer with a bare setTimeout, which meant
 * navigating away mid-message left the timer running against a detached node.
 * We keep the handle so it can be cancelled on unmount.
 */
let hideTimer = null

function showMessage(text, messageType) {
  message.value = text

  // .success / .error are what colour the text in the CSS.
  type.value = messageType
  visible.value = true

  clearTimeout(hideTimer)
  hideTimer = setTimeout(() => {
    visible.value = false
  }, 4000)
}

/** Appends the email to a JSON array in localStorage (demo only, no backend). */
function saveEmail(value) {
  const existing = readStorage(STORAGE_KEY, [])
  const list = Array.isArray(existing) ? existing : []

  list.push({ email: value, date: new Date().toISOString() })
  return writeStorage(STORAGE_KEY, list)
}

function onSubmit() {
  // No event.preventDefault() needed: @submit.prevent does it for us.
  const value = email.value.trim()

  if (!value) {
    showMessage('Please enter your email address.', 'error')
  } else if (!isValidEmail(value)) {
    showMessage('Please enter a valid email address.', 'error')
  } else {
    saveEmail(value)
    showMessage('Thanks for subscribing! Stay tuned for updates.', 'success')
    email.value = ''
  }
}

onBeforeUnmount(() => clearTimeout(hideTimer))
</script>

<template>
  <section class="newsletter section-pad" id="newsletter">
    <div class="container">
      <div class="newsletter__box">
        <h2 class="newsletter__title">Stay In The Loop</h2>

        <p class="newsletter__desc">
          Subscribe to our newsletter for the latest in development, AI and tech news.
        </p>

        <!--
          v-model keeps email.value and the input in sync by itself, so there is
          no readEmailValue() function to call before validating.
        -->
        <form class="newsletter__form" novalidate @submit.prevent="onSubmit">
          <input
            v-model="email"
            type="email"
            class="newsletter__input"
            placeholder="Enter your email address"
            aria-label="Email address"
          >
          <button type="submit" class="btn btn--primary">Subscribe</button>
        </form>

        <!-- hidden is a real HTML attribute, so v-show matches the original exactly -->
        <p
          v-show="visible"
          class="newsletter__message"
          :class="type"
          aria-live="polite"
        >
          {{ message }}
        </p>
      </div>
    </div>
  </section>
</template>
