/**
 * Content for the remaining homepage sections.
 *
 * In the original index.html each of these was literal HTML:
 *   - 3 testimonial cards, each with a hand-counted run of &#9733; spans
 *   - 3 pricing cards, each with 5 <li> bullets
 *   - 5 FAQ items, each with a hand-written button + hidden answer
 *   - the About section's two paragraphs and three value blocks
 *
 * Moving them here means the markup is written once and the content is data.
 */

/** The homepage "about" teaser section. */
export const aboutTeaser = {
  imageLabel: 'About NOVA TECH',
  tag: 'Who We Are',
  title: 'We Turn Big Ideas Into Working Products',
  story:
    'Founded in 2015, NOVA TECH has grown from a two-person studio into a full-service technology company. We partner with startups and enterprises to design, build and scale software that matters.',
  values: [
    {
      id: 'mission',
      title: 'Our Mission',
      text: 'Empower businesses with accessible, reliable and innovative technology.',
    },
    {
      id: 'vision',
      title: 'Our Vision',
      text: 'A world where every company can harness modern technology to solve real problems.',
    },
    {
      id: 'why',
      title: 'Why Choose Us',
      text: 'Expert team, transparent process, on-time delivery and long-term support.',
    },
  ],
}

/**
 * Client testimonials.
 *
 * `stars` is a NUMBER, not markup. The original wrote four &#9733; and one
 * &#9734; for the third card by hand - easy to get wrong. Now the count is
 * data and the template decides filled vs empty.
 */
export const testimonials = [
  {
    id: 1,
    stars: 5,
    quote:
      'NOVA TECH rebuilt our entire e-commerce platform. Load times dropped by 60% and conversions jumped. Truly a world-class team.',
    author: 'Sarah Mitchell',
    role: 'CEO, BrightCart',
    initials: 'S',
  },
  {
    id: 2,
    stars: 5,
    quote:
      'Their AI team delivered a recommendation engine that increased our average order value by 30%. Excellent communication throughout.',
    author: 'James Okafor',
    role: 'CTO, Finlytics',
    initials: 'J',
  },
  {
    id: 3,
    stars: 4,
    quote:
      'Professional, responsive and highly skilled. NOVA TECH secured our cloud infrastructure and modernized our legacy systems without downtime.',
    author: 'Laura Chen',
    role: 'Operations Director, MediTrust',
    initials: 'L',
  },
]

/**
 * Pricing plans.
 *
 * price is a string because the Enterprise plan says "Custom", not a number.
 * currency and period are separate because the original split them into
 * different spans for the typography.
 */
export const pricingPlans = [
  {
    id: 'starter',
    plan: 'Starter',
    price: '499',
    currency: '$',
    period: '/project',
    featured: false,
    badge: '',
    cta: 'Choose Starter',
    buttonClass: 'btn btn--outline btn--full',
    features: [
      'Landing page or small site',
      'Up to 5 pages',
      'Responsive design',
      'Basic SEO setup',
      '2-week delivery',
    ],
  },
  {
    id: 'professional',
    plan: 'Professional',
    price: '1,999',
    currency: '$',
    period: '/project',
    featured: true,
    badge: 'Most Popular',
    cta: 'Choose Professional',
    buttonClass: 'btn btn--primary btn--full',
    features: [
      'Full web application',
      'Up to 15 pages + dashboard',
      'Custom UI/UX design',
      'API integration',
      '30-day support',
    ],
  },
  {
    id: 'enterprise',
    plan: 'Enterprise',
    price: 'Custom',
    currency: '$',
    period: '',
    featured: false,
    badge: '',
    cta: 'Contact Us',
    buttonClass: 'btn btn--outline btn--full',
    features: [
      'Advanced web + mobile + AI',
      'Dedicated project team',
      'Security & compliance',
      'Cloud architecture',
      'Ongoing support & SLA',
    ],
  },
]

/**
 * FAQ entries.
 *
 * In the original this was 5 blocks of:
 *   <div class="faq-item">
 *     <button class="faq-item__question" aria-expanded="false">...</button>
 *     <div class="faq-item__answer"><p>...</p></div>
 *   </div>
 *
 * IMPORTANT CSS NOTE: .faq-item__answer uses max-height 0 -> 300px to animate.
 * So the answer element must stay in the DOM and stay inside .faq-item. We use
 * :class="{ open: ... }" rather than v-if for that reason.
 */
export const faqs = [
  {
    id: 'timeline',
    question: 'How long does a typical project take?',
    answer:
      'A standard website takes 2–4 weeks, while a full web or mobile application usually takes 6–12 weeks depending on scope. We give you a clear timeline after our discovery call.',
  },
  {
    id: 'support',
    question: 'Do you provide support after launch?',
    answer:
      'Yes. Every project includes a support period, and we also offer ongoing maintenance plans to keep your product secure and up to date.',
  },
  {
    id: 'existing-team',
    question: 'Can you work with our existing team?',
    answer:
      'Absolutely. We frequently embed with in-house teams, work in your repositories and follow your processes and tooling.',
  },
  {
    id: 'payment',
    question: 'How do you handle payment and billing?',
    answer:
      'We work on milestone-based payments. You only pay as each phase is approved, which keeps everything transparent and low-risk for you.',
  },
  {
    id: 'nda',
    question: 'Do you sign non-disclosure agreements?',
    answer:
      'Yes, we are happy to sign NDAs before any discussion of your project details or confidential information.',
  },
]
