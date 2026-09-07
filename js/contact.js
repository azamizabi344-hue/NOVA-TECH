/* ==========================================================================
   contact.js
   Drives the Contact page form:
   - Validates every field (required + format rules)
   - Shows inline error messages under each field
   - Shows a success message on a fully valid submit
   - Does NOT send anything to a server; it only saves to localStorage
     as a demo.

   Demonstrates: const, let, objects, functions, parameters, return,
   if/else, switch, for...in, array methods, template literals, DOM
   manipulation (querySelector, classList), form + keyboard events,
   localStorage + JSON, Date, and basic error handling with try/catch.
   ========================================================================== */

// --------------------------------------------------------------------------
// 1. SETUP
// --------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('contact-form');
  if (!form) return; // guard: only the contact page has this form

  initContactForm(form);
});

function initContactForm(form) {
  // Grab every field so we can reference them by name
  const fields = {
    name: document.getElementById('contact-name'),
    email: document.getElementById('contact-email'),
    phone: document.getElementById('contact-phone'),
    service: document.getElementById('contact-service'),
    subject: document.getElementById('contact-subject'),
    message: document.getElementById('contact-message'),
  };

  const successBox = document.getElementById('contact-success');

  // ------------------------------------------------------------
  // a) Validate a field as soon as the user leaves it (blur)
  // ------------------------------------------------------------
  for (const key in fields) {
    // for...in loop over the fields object
    if (!fields[key]) continue;

    // Blur event: "focus left this input"
    fields[key].addEventListener('blur', function () {
      const errorMessage = validateField(key, fields[key].value.trim());
      setFieldError(fields[key], errorMessage);
    });

    // Input event: clear the error while the user is typing again
    fields[key].addEventListener('input', function () {
      clearFieldError(fields[key]);
    });
  }

  // ------------------------------------------------------------
  // b) Submit handler: validate everything at once
  // ------------------------------------------------------------
  form.addEventListener('submit', function (event) {
    event.preventDefault(); // stop the page from refreshing

    let errorCount = 0;

    // Loop over all fields and validate each one
    for (const key in fields) {
      if (!fields[key]) continue;

      const errorMessage = validateField(key, fields[key].value.trim());

      // Set the visual error state and count invalid fields
      setFieldError(fields[key], errorMessage);
      if (errorMessage !== '') {
        errorCount++;
      }
    }

    if (errorCount === 0) {
      // Only reached when every field passed validation (if/else)
      saveContactMessage(fields);

      // Clear the form and celebrate
      form.reset();
      successBox.classList.add('show');

      // Hide the success message after a few seconds
      setTimeout(function () {
        successBox.classList.remove('show');
      }, 6000);
    } else {
      successBox.classList.remove('show');
      // Move focus to the first invalid field (the error class is set above)
      const firstError = form.querySelector('.form__group.error input, .form__group.error select, .form__group.error textarea');
      if (firstError) {
        firstError.focus();
      }
    }
  });
}

// --------------------------------------------------------------------------
// 2. VALIDATION RULES
// --------------------------------------------------------------------------
// Runs the correct check for each field. Returns an error string (or "" if
// the value is valid). Uses a switch statement to pick the rule.
function validateField(key, value) {
  switch (key) {
    case 'name':
      if (value === '') return 'Full name is required.';
      if (value.length < 2) return 'Name must be at least 2 characters.';
      return '';

    case 'email':
      if (value === '') return 'Email address is required.';
      if (!isValidEmail(value)) return 'Please enter a valid email address.';
      return '';

    case 'phone':
      // Phone is optional, but must match a phone pattern if provided
      if (value === '') return '';
      if (!isValidPhone(value)) return 'Please enter a valid phone number.';
      return '';

    case 'service':
      if (value === '') return 'Please choose a service.';
      return '';

    case 'subject':
      if (value === '') return 'Subject is required.';
      if (value.length < 3) return 'Subject must be at least 3 characters.';
      return '';

    case 'message':
      if (value === '') return 'Message is required.';
      if (value.length < 10) return 'Message must be at least 10 characters.';
      return '';

    default:
      return '';
  }
}

// Email format check using a regular expression
function isValidEmail(email) {
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailPattern.test(email);
}

// Phone format check: optional +, then 7-15 digits
// with spaces, dashes or parentheses allowed
function isValidPhone(phone) {
  const phonePattern = /^[+]?[\d\s\-()]{7,15}$/;
  return phonePattern.test(phone);
}

// --------------------------------------------------------------------------
// 3. ERROR DISPLAY HELPERS
// --------------------------------------------------------------------------
// Finds the closest .form__group for an input and shows an error message
function setFieldError(input, message) {
  const group = input.closest('.form__group');
  if (!group) return;

  const errorElement = group.querySelector('.form__error');

  if (message) {
    // Invalid: show the message and add the error class
    errorElement.textContent = message;
    group.classList.add('error');
  } else {
    // Valid: clear the message and remove the error class
    errorElement.textContent = '';
    group.classList.remove('error');
  }
}

// Removes the error state from one field
function clearFieldError(input) {
  const group = input.closest('.form__group');
  if (!group) return;

  group.classList.remove('error');

  const errorElement = group.querySelector('.form__error');
  if (errorElement) {
    errorElement.textContent = '';
  }
}

// --------------------------------------------------------------------------
// 4. SAVE THE MESSAGE (localStorage demo only — nothing is sent anywhere)
// --------------------------------------------------------------------------
function saveContactMessage(fields) {
  try {
    const storageKey = 'novatech_contact_messages';
    const rawData = localStorage.getItem(storageKey);

    // Parse existing messages; default to an empty array
    let messages = JSON.parse(rawData);
    if (!Array.isArray(messages)) {
      messages = [];
    }

    // Build a message object from the current field values
    const message = {
      id: Date.now(), // unique-ish id using the Date object
      name: fields.name.value.trim(),
      email: fields.email.value.trim(),
      phone: fields.phone.value.trim(),
      service: fields.service.value,
      subject: fields.subject.value.trim(),
      text: fields.message.value.trim(),
      createdAt: new Date().toISOString(),
    };

    // Add it to the start of the list and store it
    messages.unshift(message);
    localStorage.setItem(storageKey, JSON.stringify(messages));

    console.log('Message saved locally (demo only):', message.name);
  } catch (error) {
    // Reached if localStorage is unavailable (private mode, etc.)
    console.error('Could not save contact message:', error);
  }
}