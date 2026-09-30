/**
 * Content for the Careers page.
 *
 * The original careers.html was static HTML with no careers.js - the whole
 * page was 5 blocks of hand-written markup. The most repetitive part was the
 * 7 job cards: 11 lines each, 77 lines total, differing only in department,
 * type, title, experience level and description.
 */

/**
 * "Why People Love Working Here" - four cards.
 *
 * These reuse .value-card from the About page's Core Values, so they are the
 * same component with different content. Icons are \u{...} escapes with the
 * glyph in a comment, so the file cannot be corrupted by a wrong encoding.
 */
export const cultureValues = [
  {
    id: 'impact',
    icon: '\u{1F680}', // 🚀 rocket
    title: 'Real Impact',
    text: 'Your work ships to real users within weeks, not years.',
  },
  {
    id: 'grow',
    icon: '\u{1F4C8}', // 📈 chart increasing
    title: 'Grow Fast',
    text: 'Learning budget, mentorship and interesting problems.',
  },
  {
    id: 'remote',
    icon: '\u{1F3D6}', // 🏖 beach with umbrella (the original had no VS16)
    title: 'Remote Friendly',
    text: 'Work from anywhere, with async-first communication.',
  },
  {
    id: 'fair',
    icon: '\u{1F381}', // 🎁 wrapped gift
    title: 'Fair & Flexible',
    text: 'Competitive pay, flexible hours and time to recharge.',
  },
]

/**
 * The 7 open positions.
 *
 * Fields:
 *   id          number  stable key for v-for
 *   department  string  shown in .job-card__dept
 *   type        string  shown in .job-card__type
 *   title       string
 *   meta        string  "Tech City · 3+ years experience"
 *   description string
 *
 * NOTE: there is deliberately NO filter or search here. The original careers
 * page had none - all 7 roles are always shown. Adding chips would be a feature
 * the source does not have, so `useExplore` is not used on this page.
 */
export const jobs = [
  {
    id: 1,
    department: 'Engineering',
    type: 'Full-time / Remote',
    title: 'Senior Frontend Developer',
    meta: 'Tech City · 3+ years experience',
    description:
      'Build fast, accessible interfaces with vanilla JavaScript and modern CSS. You will own features from design handoff to deploy.',
  },
  {
    id: 2,
    department: 'Engineering',
    type: 'Full-time / Remote',
    title: 'Backend Developer (Node.js)',
    meta: 'Tech City · 2+ years experience',
    description:
      'Design and build the APIs and databases behind our products. You care about reliability, clean code and clear documentation.',
  },
  {
    id: 3,
    department: 'AI',
    type: 'Full-time / Remote',
    title: 'Machine Learning Engineer',
    meta: 'Tech City · 3+ years experience',
    description:
      "Train, evaluate and ship ML models. You know Python deeply and can explain a model's limits as clearly as its strengths.",
  },
  {
    id: 4,
    department: 'Security',
    type: 'Full-time / Hybrid',
    title: 'Penetration Tester',
    meta: 'Tech City · 3+ years experience',
    description:
      "Test our clients' applications and infrastructure like a real attacker. Write reports that teams can actually act on.",
  },
  {
    id: 5,
    department: 'Design',
    type: 'Full-time / Hybrid',
    title: 'UI/UX Designer',
    meta: 'Tech City · 2+ years experience',
    description:
      'Own the experience of our products end to end — research, wireframes, prototypes and a design system that scales.',
  },
  {
    id: 6,
    department: 'Delivery',
    type: 'Full-time / Remote',
    title: 'Project Manager',
    meta: 'Tech City · 3+ years experience',
    description:
      'Keep projects on time and clients happy. You translate between business goals and technical reality with ease.',
  },
  {
    id: 7,
    department: 'Engineering',
    type: 'Internship',
    title: 'Junior Developer Intern',
    meta: 'Tech City · No experience required',
    description:
      'Our 12-week paid internship is the best way to start a career in tech. Learn by building real features with a mentor by your side.',
  },
]
