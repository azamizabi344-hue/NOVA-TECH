<script setup>
import { ref, reactive, onBeforeUnmount, nextTick } from 'vue'
import { contactFields, serviceOptions } from '@/data/contact'
import { validateField } from '@/composables/useContactForm'
import { readStorage, writeStorage } from '@/utils/storage'

const STORAGE_KEY = 'novatech_contact_messages'

/**
 * ContactForm - the message form.
 *
 * This replaces the majority of the original contact.js (226 lines). The key
 * difference is that there is no per-field event wiring at all.
 *
 * ORIGINAL, per field:
 *   fields[key].addEventListener('blur',    ...)   // validate on leave
 *   fields[key].addEventListener('input',   ...)   // clear error while typing
 * ...repeated for all 6 fields, inside a for...in loop.
 *
 * NOW, once in the template:
 *   @blur="validateOne(field.key)"   @input="clearError(field.key)"
 *
 * The template generates those listeners from the same `contactFields` list that
 * generates the markup, so a new field cannot forget to be validated.
 */
const values = reactive({})
const errors = reactive({})
const successVisible = ref(false)

let successTimer = null

/** Validates a single field and stores the message (or ''). */
function validateOne(key) {
  errors[key] = validateField(key, values[key] ?? '')
}

/** Clears one field's error as soon as the user starts fixing it. */
function clearError(key) {
  errors[key] = ''
}

function onSubmit() {
  // No event.preventDefault() - @submit.prevent does it.
  let errorCount = 0
  let firstBadKey = null

  for (const field of contactFields) {
    const message = validateField(field.key, values[field.key] ?? '')
    errors[field.key] = message

    if (message !== '') {
      errorCount++
      if (!firstBadKey) firstBadKey = field.key
    }
  }

  if (errorCount > 0) {
    successVisible.value = false

    // Move focus to the first invalid field, as the original did. A template
    // ref is used instead of form.querySelector('.form__group.error input').
    if (firstBadKey) {
      nextTick(() => fieldRefs[firstBadKey]?.focus())
    }
    return
  }

  save(values)
  resetForm()
  showSuccess()
}

/**
 * Refs to the actual form controls, so focus can be moved imperatively.
 * Keys match the field `key` values.
 */
const fieldRefs = {}

/** Called from the template via a :ref function, one per control. */
function setFieldRef(key, el) {
  if (el) fieldRefs[key] = el
  else delete fieldRefs[key]
}

function save(data) {
  const existing = readStorage(STORAGE_KEY, [])
  const messages = Array.isArray(existing) ? existing : []

  // unshift() puts the newest at the front, which is what the dashboard's
  // message panel expects to display first.
  messages.unshift({
    id: Date.now(),
    name: data.name.trim(),
    email: data.email.trim(),
    phone: data.phone.trim(),
    service: data.service,
    subject: data.subject.trim(),
    text: data.message.trim(),
    createdAt: new Date().toISOString(),
  })

  writeStorage(STORAGE_KEY, messages)
}

function resetForm() {
  for (const field of contactFields) {
    values[field.key] = ''
    errors[field.key] = ''
  }
}

function showSuccess() {
  successVisible.value = true
  clearTimeout(successTimer)
  successTimer = setTimeout(() => {
    successVisible.value = false
  }, 6000)
}

onBeforeUnmount(() => clearTimeout(successTimer))
</script>

<template>
  <div class="contact-form-wrap">
    <h2 class="form__title">Send Us A Message</h2>

    <!--
      v-show rather than v-if: the original added/removed a .show class on an
      always-present element, and the CSS may animate it.
    -->
    <div v-show="successVisible" class="form__success" role="status">
      Thank you! Your message has been received. We will reply within 24 hours.
    </div>

    <!-- novalidate: we validate ourselves, so the browser's bubbles are off -->
    <form novalidate @submit.prevent="onSubmit">
      <!--
        The original had 3 .form__row blocks. This groups the first four
        fields into two rows of two, exactly as before, driven by `row: true`
        in the data. The last two fields have no row and stack full width.
      -->
      <div class="form__row">
        <div
          v-for="field in contactFields.filter((f) => f.row)"
          :key="field.key"
          class="form__group"
          :class="{ error: errors[field.key] }"
        >
          <label class="form__label" :for="`contact-${field.key}`">
            {{ field.label }}
            <span v-if="field.required" class="form__required">*</span>
          </label>

          <select
            v-if="field.type === 'select'"
            :id="`contact-${field.key}`"
            v-model="values[field.key]"
            class="form__select"
            @blur="validateOne(field.key)"
            @change="validateOne(field.key)"
          >
            <option v-for="option in field.options ?? serviceOptions" :key="option.value" :value="option.value">
              {{ option.label }}
            </option>
          </select>

          <input
            v-else
            :id="`contact-${field.key}`"
            :ref="(el) => setFieldRef(field.key, el)"
            v-model="values[field.key]"
            :type="field.type"
            class="form__input"
            :placeholder="field.placeholder"
            :autocomplete="field.autocomplete"
            @blur="validateOne(field.key)"
            @input="clearError(field.key)"
          >

          <p class="form__error">{{ errors[field.key] }}</p>
        </div>
      </div>

      <!-- Subject and Message: full width, no .form__row -->
      <div
        v-for="field in contactFields.filter((f) => !f.row)"
        :key="field.key"
        class="form__group"
        :class="{ error: errors[field.key] }"
      >
        <label class="form__label" :for="`contact-${field.key}`">
          {{ field.label }}
          <span v-if="field.required" class="form__required">*</span>
        </label>

        <textarea
          v-if="field.type === 'textarea'"
          :id="`contact-${field.key}`"
          :ref="(el) => setFieldRef(field.key, el)"
          v-model="values[field.key]"
          class="form__textarea"
          :placeholder="field.placeholder"
          @blur="validateOne(field.key)"
          @input="clearError(field.key)"
        ></textarea>

        <input
          v-else
          :id="`contact-${field.key}`"
          :ref="(el) => setFieldRef(field.key, el)"
          v-model="values[field.key]"
          :type="field.type"
          class="form__input"
          :placeholder="field.placeholder"
          @blur="validateOne(field.key)"
          @input="clearError(field.key)"
        >

        <p class="form__error">{{ errors[field.key] }}</p>
      </div>

      <button type="submit" class="btn btn--primary btn--full">Send Message</button>
    </form>
  </div>
</template>
