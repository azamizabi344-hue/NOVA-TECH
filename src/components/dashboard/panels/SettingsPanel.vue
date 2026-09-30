<script setup>
import { ref, onBeforeUnmount } from 'vue'
import { readStorage, writeStorage } from '@/utils/storage'
import { useAuth } from '@/composables/useAuth'
import { DISPLAY_NAME_KEY } from '@/data/dashboard'

/**
 * SettingsPanel - the profile form.
 *
 * Replaces initSettingsForm(user), which read the saved display name (falling
 * back to the session user's name), filled three inputs by hand, and saved the
 * new name on submit.
 *
 * The user comes from useAuth() rather than a prop, so DashboardView can render
 * all eight panels with one uniform `<component :is="activeComponent" />`. Passing
 * :user to every panel would attach it as a fallthrough attribute on the seven
 * that do not declare it, putting user="[object Object]" in the DOM.
 */
const { currentUser } = useAuth()
const user = currentUser

/**
 * Prefill from storage, falling back to the session user's name - identical to
 * `localStorage.getItem(DISPLAY_NAME_KEY) || user.name`.
 *
 * The ?? chain also covers an empty string, which `||` handled in the original.
 */
const storedName = readStorage(DISPLAY_NAME_KEY, null)
const displayName = ref(
  typeof storedName === 'string' && storedName !== '' ? storedName : user.value.name,
)

const successVisible = ref(false)
let successTimer = null

function onSubmit() {
  const newName = displayName.value.trim()

  if (newName === '') {
    alert('Display name cannot be empty.')
    return
  }

  writeStorage(DISPLAY_NAME_KEY, newName)
  console.log('Display name saved:', newName)

  successVisible.value = true
  clearTimeout(successTimer)
  successTimer = setTimeout(() => {
    successVisible.value = false
  }, 4000)
}

onBeforeUnmount(() => clearTimeout(successTimer))
</script>

<template>
  <div class="panel__header">
    <div>
      <h2 class="panel__title">Settings</h2>
      <p class="panel__sub">Update your profile preferences (demo only).</p>
    </div>
  </div>

  <div class="panel-card">
    <h2 class="panel-card__title">Profile</h2>

    <form class="project-form" @submit.prevent="onSubmit">
      <div class="form__group">
        <label class="form__label" for="set-name">Display Name</label>
        <input id="set-name" v-model="displayName" type="text" class="form__input" placeholder="Your name">
      </div>

      <div class="form__group">
        <label class="form__label" for="set-email">Email (read-only)</label>
        <input id="set-email" type="email" class="form__input" :value="user.email" readonly>
      </div>

      <div class="form__group">
        <label class="form__label" for="set-role">Role (read-only)</label>
        <input id="set-role" type="text" class="form__input" :value="user.role" readonly>
      </div>

      <button type="submit" class="btn btn--primary">Save Settings</button>
    </form>

    <div v-show="successVisible" class="dash-success">Settings saved successfully.</div>
  </div>
</template>
