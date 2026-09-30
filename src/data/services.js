/**
 * The service catalogue - ONE source of truth.
 *
 * In the original project this list existed in three places:
 *   1. 6 hardcoded <article class="service-card"> blocks in index.html
 *   2. servicesData in js/services.js (6 items, full detail)
 *   3. servicesList in js/dashboard.js (6 items, short)
 *
 * Now there is one array. The homepage reads shortDesc + icon, the services
 * page reads everything, and the dashboard panel reads title + shortDesc.
 *
 * Fields:
 *   id            number  stable key for v-for
 *   title         string  the full name ("Mobile App Development")
 *   shortTitle    string  the name used on the homepage ("Mobile Development")
 *   category      string  web | mobile | ai | security | cloud | design
 *   icon          string  which ServiceIcon to render
 *   shortDesc     string  one-liner for the homepage grid
 *   description   string  one-liner for the services page card
 *   longDescription string  paragraph for the detail modal
 *   features      string[] exactly 5 bullet points
 *   startingPrice number  USD, formatted with toLocaleString() as $1,499
 */
export const services = [
  {
    id: 1,
    title: 'Web Development',
    shortTitle: 'Web Development',
    category: 'web',
    icon: 'web',
    shortDesc:
      'Fast, responsive and SEO-friendly websites and web applications built with modern, maintainable code.',
    description:
      'Custom web products engineered for speed, accessibility and long-term maintainability.',
    longDescription:
      'We build fast, accessible and search-friendly websites and web applications. Every project starts with a clear information architecture, then moves through design, engineering, testing and launch - so what you receive is genuinely maintainable long after we hand it over.',
    features: [
      'Responsive, mobile-first design',
      'SEO optimisation from day one',
      'Accessible markup (WCAG)',
      'CMS or custom admin options',
      'Performance budget & audit',
    ],
    startingPrice: 499,
  },
  {
    id: 2,
    title: 'Mobile App Development',
    shortTitle: 'Mobile Development',
    category: 'mobile',
    icon: 'mobile',
    shortDesc:
      'Native and cross-platform mobile apps for iOS and Android with smooth, delightful user experiences.',
    description:
      'Native and cross-platform apps for iOS and Android, built for smooth, reliable everyday use.',
    longDescription:
      'From App Store launches to internal enterprise tools, we design and build mobile applications for iOS and Android. We choose between native and cross-platform based on your budget and roadmap, then optimise for real-device performance.',
    features: [
      'iOS & Android delivery',
      'Native or cross-platform',
      'Offline-first data handling',
      'Push notifications',
      'Store submission & release',
    ],
    startingPrice: 1499,
  },
  {
    id: 3,
    title: 'AI Solutions',
    shortTitle: 'AI Solutions',
    category: 'ai',
    icon: 'ai',
    shortDesc:
      'Intelligent automation, machine learning models and data-driven insights that power smarter decisions.',
    description:
      'Intelligent automation and machine learning models that turn your data into decisions.',
    longDescription:
      'We help teams move from "we should use AI" to something in production. That means practical automation, carefully validated machine learning models, and dashboards that make the results legible to the people who need to act on them.',
    features: [
      'Process & workflow automation',
      'Machine learning models',
      'Natural language processing',
      'Data pipelines & preparation',
      'Insight dashboards',
    ],
    startingPrice: 2999,
  },
  {
    id: 4,
    title: 'Cyber Security',
    shortTitle: 'Cyber Security',
    category: 'security',
    icon: 'security',
    shortDesc:
      'Proactive security audits, penetration testing and protection plans that keep your data and systems safe.',
    description:
      'Security audits, penetration testing and protection plans that keep your systems safe.',
    longDescription:
      'Security is a process, not a product. We run structured audits and authorised penetration tests, produce findings your engineers can actually fix, and leave behind the monitoring and process changes that keep the risk down afterwards.',
    features: [
      'Full security audit',
      'Authorised penetration testing',
      'Threat & risk assessment',
      'Security training for teams',
      'Ongoing monitoring setup',
    ],
    startingPrice: 999,
  },
  {
    id: 5,
    title: 'Cloud Computing',
    shortTitle: 'Cloud Computing',
    category: 'cloud',
    icon: 'cloud',
    shortDesc:
      'Scalable cloud architecture, migration and DevOps that reduce cost and boost performance.',
    description:
      'Scalable cloud architecture, migration and DevOps that cut costs and lift performance.',
    longDescription:
      'We design cloud architecture that scales when you do and does not cost more than it needs to. That covers migration planning, infrastructure as code, CI/CD pipelines and the guardrails that keep deployments boring and reversible.',
    features: [
      'Cloud migration & planning',
      'Infrastructure as code',
      'CI/CD pipeline setup',
      'Cost optimisation review',
      'Backup & disaster recovery',
    ],
    startingPrice: 799,
  },
  {
    id: 6,
    title: 'UI/UX Design',
    shortTitle: 'UI/UX Design',
    category: 'design',
    icon: 'design',
    shortDesc:
      'Human-centered interfaces and delightful experiences that users love and that convert.',
    description:
      'Human-centered interfaces and experiences that users enjoy and that actually convert.',
    longDescription:
      'Good design is a business outcome, not decoration. We research your users, prototype early, and iterate with real feedback - then hand over a design system your engineers can build against without guesswork.',
    features: [
      'User research & personas',
      'Wireframing & prototyping',
      'Design systems & components',
      'Accessibility review',
      'Usability testing',
    ],
    startingPrice: 599,
  },
]

/**
 * The category chips for the services page filter bar.
 * The homepage does NOT show these - it shows all six services.
 */
export const serviceFilters = [
  { value: 'all', label: 'All' },
  { value: 'web', label: 'Web' },
  { value: 'mobile', label: 'Mobile' },
  { value: 'ai', label: 'AI' },
  { value: 'security', label: 'Security' },
  { value: 'cloud', label: 'Cloud' },
  { value: 'design', label: 'Design' },
]

/**
 * Display labels for the detail modal's category badge.
 *
 * The original did this with a switch statement inside openServiceDetails():
 *
 *   switch (service.category) {
 *     case 'web':      categoryLabel = 'Web Development'; break;
 *     case 'mobile':   categoryLabel = 'Mobile Development'; break;
 *     ...
 *   }
 *
 * A switch that only maps strings to strings is really just a lookup table, and
 * a lookup table belongs next to the data, not inside a click handler. The
 * default case in the original fell back to the raw category - the `??` below
 * does the same.
 */
export const serviceCategoryLabels = {
  web: 'Web Development',
  mobile: 'Mobile Development',
  ai: 'AI Solutions',
  security: 'Cyber Security',
  cloud: 'Cloud Computing',
  design: 'UI/UX Design',
}
