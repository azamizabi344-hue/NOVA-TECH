/**
 * Content for the Contact page.
 *
 * The left-hand info column was 4 nearly identical cards in contact.html, each
 * an emoji plus a heading and a line of text.
 *
 * Icons are \u{...} escapes with the glyph in a comment so the file cannot be
 * corrupted by a wrong encoding.
 */
export const contactInfo = [
  {
    id: 'visit',
    icon: '\u{1F4CD}', // 📍 round pushpin
    title: 'Visit Us',
    text: '123 Innovation Drive, Tech City, TC 12345',
  },
  {
    id: 'call',
    icon: '\u{1F4DE}', // 📞 telephone receiver
    title: 'Call Us',
    text: '+1 (555) 123-4567',
  },
  {
    id: 'email',
    icon: '\u{2709}\u{FE0F}', // ✉️ envelope (FE0F = variation selector)
    title: 'Email Us',
    text: 'hello@novatech.com',
  },
  {
    id: 'hours',
    icon: '\u{23F1}\u{FE0F}', // ⏱ stopwatch (FE0F = variation selector)
    title: 'Working Hours',
    text: 'Mon - Fri, 9:00 AM - 6:00 PM',
  },
]

/**
 * The <select> options for "Service Needed".
 *
 * The empty first option is the "Select a service..." placeholder, and it is
 * also what makes the field FAIL validation when untouched - the original
 * treated an empty service value as an error, which is why a placeholder that
 * has no value is required rather than optional.
 */
export const serviceOptions = [
  { value: '', label: 'Select a service...' },
  { value: 'web', label: 'Web Development' },
  { value: 'mobile', label: 'Mobile Development' },
  { value: 'ai', label: 'AI Solutions' },
  { value: 'security', label: 'Cyber Security' },
  { value: 'cloud', label: 'Cloud Computing' },
  { value: 'design', label: 'UI/UX Design' },
  { value: 'other', label: 'Other' },
]

/**
 * The form's field definitions - used to build the inputs AND to drive
 * validation from one list, instead of a switch statement plus 6 hand-written
 * blocks of HTML.
 *
 * Each entry:
 *   key       the property name, also the saved-message key
 *   label     visible label text
 *   type      input type, or 'select' / 'textarea'
 *   required  shows the red asterisk
 *   placeholder
 *   row       true = share a .form__row with the next field
 *   minLength used by the validator
 *   options   only for type: 'select'
 */
export const contactFields = [
  {
    key: 'name',
    label: 'Full Name',
    type: 'text',
    required: true,
    placeholder: 'John Doe',
    autocomplete: 'name',
    row: true,
  },
  {
    key: 'email',
    label: 'Email Address',
    type: 'email',
    required: true,
    placeholder: 'john@example.com',
    autocomplete: 'email',
    row: true,
  },
  {
    key: 'phone',
    label: 'Phone Number',
    type: 'tel',
    required: false,
    placeholder: '+1 555 123 4567',
    autocomplete: 'tel',
    row: true,
  },
  {
    key: 'service',
    label: 'Service Needed',
    type: 'select',
    required: true,
    row: true,
    options: serviceOptions,
  },
  {
    key: 'subject',
    label: 'Subject',
    type: 'text',
    required: true,
    placeholder: 'Project inquiry',
    minLength: 3,
  },
  {
    key: 'message',
    label: 'Message',
    type: 'textarea',
    required: true,
    placeholder: 'Tell us about your project...',
    minLength: 10,
  },
]
