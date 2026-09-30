import { isValidEmail, isValidPhone } from '@/utils/strings'

/**
 * Validates one field of the contact form.
 *
 * Replaces the switch statement in the original contact.js:
 *
 *   function validateField(key, value) {
 *     switch (key) {
 *       case 'name':    if (value === '') return 'Full name is required.'; ...
 *       case 'email':   if (!isValidEmail(value)) return '...'; ...
 *       case 'phone':   if (value === '') return '';  // optional!
 *       ...
 *     }
 *   }
 *
 * The rules are now DATA, next to the field definitions in src/data/contact.js,
 * so a field and its validation cannot drift apart.
 */
export const validationRules = {
  name: {
    required: true,
    requiredMessage: 'Full name is required.',
    minLength: 2,
    minLengthMessage: 'Name must be at least 2 characters.',
  },
  email: {
    required: true,
    requiredMessage: 'Email address is required.',
    format: 'email',
    formatMessage: 'Please enter a valid email address.',
  },
  phone: {
    // Optional: an empty value is VALID, not an error. But if you do type
    // something it must look like a phone number.
    required: false,
    format: 'phone',
    formatMessage: 'Please enter a valid phone number.',
  },
  service: {
    required: true,
    requiredMessage: 'Please choose a service.',
  },
  subject: {
    required: true,
    requiredMessage: 'Subject is required.',
    minLength: 3,
    minLengthMessage: 'Subject must be at least 3 characters.',
  },
  message: {
    required: true,
    requiredMessage: 'Message is required.',
    minLength: 10,
    minLengthMessage: 'Message must be at least 10 characters.',
  },
}

const formatters = {
  email: isValidEmail,
  phone: isValidPhone,
}

/**
 * @returns {string} an error message, or '' when the value is valid.
 */
export function validateField(key, value) {
  const rule = validationRules[key]
  if (!rule) return ''

  // The RAW value is validated, exactly like the original:
  //   if (value === '') ...        if (value.length < 2) ...
  // Trimming here would change behaviour in two observable ways:
  //   - a name of "   " would report "required" instead of "at least 2 characters"
  //   - "  john@x.com  " would be ACCEPTED, because the email regex is anchored
  //     and the original rejects the stray spaces
  // The form trims on save instead, which is where the original trims.
  const raw = String(value ?? '')

  if (raw === '') {
    // An optional field left blank is fine.
    return rule.required ? rule.requiredMessage : ''
  }

  if (rule.minLength && raw.length < rule.minLength) {
    return rule.minLengthMessage
  }

  if (rule.format && !formatters[rule.format](raw)) {
    return rule.formatMessage
  }

  return ''
}

/** Validates every field at once. @returns {string[]} the keys that failed. */
export function validateAll(values) {
  return Object.keys(validationRules).filter((key) => validateField(key, values[key] ?? '') !== '')
}
