/**
 * The project catalogue - 12 projects, copied verbatim from the original
 * js/projects.js `projectsData` array.
 *
 * Fields:
 *   id              number   stable key for v-for
 *   name            string   card + modal title
 *   category        string   web | mobile | ai | security
 *   imageText       string   the big gradient label (BRIGHT, ORBIT, ...)
 *   description     string   short line on the card
 *   longDescription string   the paragraph shown in the modal
 *   technologies    string[] 3-5 chips on the card
 *   client          string   shown in the modal
 *   year            number   used by the sort dropdown
 */
export const projects = [
  {
    id: 1,
    name: 'BrightCart Commerce Platform',
    category: 'web',
    imageText: 'BRIGHT',
    description: 'A blazing-fast e-commerce platform rebuilt from a slow legacy monolith.',
    longDescription:
      "We modernized BrightCart's entire online store, introduced a modular architecture and cut page load times by 60%, which directly lifted conversion rates across their catalog.",
    technologies: ['HTML5', 'CSS3', 'JavaScript (ES6+)', 'Node.js', 'PostgreSQL'],
    client: 'BrightCart Inc.',
    year: 2025,
  },
  {
    id: 2,
    name: 'Finlytics Analytics Dashboard',
    category: 'web',
    imageText: 'FINLYTICS',
    description: 'Real-time financial analytics that turned raw data into clear decisions.',
    longDescription:
      'A custom dashboard with live charts, role-based access and automated reports. Finlytics analysts now answer in minutes what used to take a full afternoon.',
    technologies: ['JavaScript', 'TypeScript', 'MongoDB', 'Docker'],
    client: 'Finlytics Ltd.',
    year: 2024,
  },
  {
    id: 3,
    name: 'MediTrust Patient Portal',
    category: 'web',
    imageText: 'MEDI',
    description: 'A secure patient portal connecting clinics with thousands of patients.',
    longDescription:
      'We built a HIPAA-aware portal with appointment booking, records access and secure messaging. A clean, accessible interface for patients of all ages.',
    technologies: ['HTML5', 'CSS3', 'JavaScript', 'PostgreSQL', 'AWS'],
    client: 'MediTrust Health',
    year: 2024,
  },
  {
    id: 4,
    name: 'OrbitRide Ride-Hailing App',
    category: 'mobile',
    imageText: 'ORBIT',
    description: 'A ride-hailing mobile app with live tracking and instant booking.',
    longDescription:
      'Drivers and riders connect through real-time GPS, in-app payments and a smart dispatch engine. Over 100k rides booked in the first year.',
    technologies: ['Kotlin', 'Firebase', 'Google Maps API'],
    client: 'OrbitRide',
    year: 2025,
  },
  {
    id: 5,
    name: 'FitPulse Fitness Tracker',
    category: 'mobile',
    imageText: 'FITPULSE',
    description: 'A health app that turns daily activity into motivating workout plans.',
    longDescription:
      'FitPulse syncs with wearables, generates adaptive training plans and keeps users accountable with streaks, goals and friendly in-app challenges.',
    technologies: ['Swift', 'HealthKit', 'Core ML'],
    client: 'FitPulse',
    year: 2024,
  },
  {
    id: 6,
    name: 'TravelBee Trip Planner',
    category: 'mobile',
    imageText: 'TRAVELBEE',
    description: 'A cross-platform trip planner with offline maps and smart itineraries.',
    longDescription:
      'One codebase, two stores. TravelBee helps travellers organise flights, hotels and day plans, then works offline anywhere in the world.',
    technologies: ['Flutter', 'REST APIs', 'Firebase'],
    client: 'TravelBee',
    year: 2023,
  },
  {
    id: 7,
    name: 'Sentra AI Support Assistant',
    category: 'ai',
    imageText: 'SENTRA',
    description: 'An intelligent support assistant that resolves 80% of tickets automatically.',
    longDescription:
      'Trained on years of support history, Sentra answers common questions instantly and routes the rest to the right human — cutting first-response time by 90%.',
    technologies: ['Python', 'TensorFlow', 'Node.js', 'Redis'],
    client: 'Sentra Corp',
    year: 2025,
  },
  {
    id: 8,
    name: 'VisionGuard Defect Detection',
    category: 'ai',
    imageText: 'VISION',
    description: 'Computer vision that spots production defects in real time.',
    longDescription:
      'A camera-based ML pipeline inspects thousands of units per hour and flags defects the human eye misses, reducing waste by 35% on the factory floor.',
    technologies: ['Python', 'PyTorch', 'OpenCV', 'Docker'],
    client: 'Duralight Manufacturing',
    year: 2024,
  },
  {
    id: 9,
    name: 'InsightIQ Sales Forecasting',
    category: 'ai',
    imageText: 'INSIGHT',
    description: 'A forecasting engine that predicts revenue with 94% accuracy.',
    longDescription:
      'InsightIQ combines market data and internal history to forecast sales by region and channel, giving leadership a confident view months ahead.',
    technologies: ['Python', 'Scikit-learn', 'Pandas', 'AWS'],
    client: 'InsightIQ',
    year: 2023,
  },
  {
    id: 10,
    name: 'SecureBank Fraud Shield',
    category: 'security',
    imageText: 'SECURE',
    description: 'Real-time fraud detection protecting millions of banking transactions.',
    longDescription:
      "We hardened SecureBank's transaction flow with behavioural analytics and anomaly detection, blocking fraudulent activity while keeping false positives low.",
    technologies: ['Python', 'AWS', 'Kubernetes'],
    client: 'SecureBank',
    year: 2025,
  },
  {
    id: 11,
    name: 'AuthShield Identity Platform',
    category: 'security',
    imageText: 'AUTH',
    description: 'A single sign-on and identity platform trusted by enterprise teams.',
    longDescription:
      'AuthShield delivers SSO, MFA and role-based access across every internal app, with zero-trust policies enforced everywhere by default.',
    technologies: ['JavaScript', 'Node.js', 'OAuth 2.0', 'PostgreSQL'],
    client: 'AuthShield',
    year: 2024,
  },
  {
    id: 12,
    name: 'CyberSentinel Threat Monitor',
    category: 'security',
    imageText: 'CYBER',
    description: 'A 24/7 security monitoring center that detects threats before they spread.',
    longDescription:
      'CyberSentinel aggregates logs from hundreds of sources, correlates them with known attack patterns and alerts security teams in under a minute.',
    technologies: ['Python', 'Elastic Stack', 'Docker', 'Kubernetes'],
    client: 'Sentinel Systems',
    year: 2023,
  },
]

/** How many projects the homepage "Featured" section shows. */
export const FEATURED_LIMIT = 6

/**
 * The 5 category chips. Used by BOTH the homepage featured section and the
 * projects page - the original project duplicated this exact bar in index.html
 * and projects.html.
 */
export const projectFilters = [
  { value: 'all', label: 'All' },
  { value: 'web', label: 'Web' },
  { value: 'mobile', label: 'Mobile' },
  { value: 'ai', label: 'AI' },
  { value: 'security', label: 'Security' },
]

/** The 3 options in the projects page sort dropdown. */
export const projectSortOptions = [
  { value: 'newest', label: 'Newest First' },
  { value: 'oldest', label: 'Oldest First' },
  { value: 'az', label: 'Name (A-Z)' },
]
