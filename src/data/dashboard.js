/**
 * Static seed data + UI metadata for the admin dashboard.
 *
 * Everything here was a `const` at the top of the original dashboard.js. Keeping
 * it in one module means the panels stay purely presentational.
 *
 * Icons are \u{...} escapes (with the glyph in a comment) instead of the
 * original's HTML entities, because these are rendered as TEXT by Vue - an
 * entity like '&#128187;' would print literally rather than becoming 💻.
 */

/** Where the managed project list is persisted. */
export const PROJECTS_KEY = 'novatech_dashboard_projects'
/** Where the Settings panel saves the display name. */
export const DISPLAY_NAME_KEY = 'novatech_display_name'
/** Written by the contact form, read by the Overview + Messages panels. */
export const CONTACT_MESSAGES_KEY = 'novatech_contact_messages'

/** The project-management list starts with these six rows. */
export const initialProjects = [
  { id: 1, name: 'BrightCart Commerce Platform', category: 'web', client: 'BrightCart Inc.', year: 2025, status: 'done' },
  { id: 2, name: 'Sentra AI Support Assistant', category: 'ai', client: 'Sentra Corp', year: 2025, status: 'active' },
  { id: 3, name: 'OrbitRide Ride-Hailing App', category: 'mobile', client: 'OrbitRide', year: 2025, status: 'active' },
  { id: 4, name: 'SecureBank Fraud Shield', category: 'security', client: 'SecureBank', year: 2025, status: 'done' },
  { id: 5, name: 'MediTrust Patient Portal', category: 'web', client: 'MediTrust Health', year: 2024, status: 'done' },
  { id: 6, name: 'FitPulse Fitness Tracker', category: 'mobile', client: 'FitPulse', year: 2024, status: 'pending' },
]

/** Fixed company-wide numbers. `projects` is a base, adjusted by what you add. */
export const baseStats = {
  projects: 150,
  users: 1248,
  team: 50,
  intents: 12,
  revenue: '$2.4M',
}

/**
 * The six overview cards.
 *
 * `source` says where each value comes from, so the card component does not
 * need a switch statement - it just renders `value`.
 */
export const overviewCards = [
  { id: 'projects', label: 'Total Projects', source: 'totalProjects', trend: '\u{25B2} +8 this year' },
  { id: 'users', label: 'Total Users', source: 'users', trend: '\u{25B2} +120 this month' },
  { id: 'messages', label: 'Messages', source: 'messageCount', trend: '\u{25B2} 3 unread' },
  { id: 'team', label: 'Team Members', source: 'team', trend: '\u{25B2} 2 new hires' },
  { id: 'intents', label: 'Intents', source: 'intents', trend: '\u{25B2} 6 active now' },
  { id: 'revenue', label: 'Revenue', source: 'revenue', trend: '\u{25B2} +18% vs last year' },
]

/** Shown when the contact form has not been submitted yet. */
export const sampleMessages = [
  {
    name: 'Sarah Mitchell',
    email: 'sarah@brightcart.com',
    subject: 'E-commerce replatform',
    text: 'We would love a quote for rebuilding our storefront.',
    createdAt: '2026-09-02T10:00:00.000Z',
  },
  {
    name: 'James Okafor',
    email: 'james@finlytics.com',
    subject: 'AI roadmap',
    text: 'Interested in a chatbot and predictive analytics pilot.',
    createdAt: '2026-08-28T09:30:00.000Z',
  },
  {
    name: 'Laura Chen',
    email: 'laura@meditrust.com',
    subject: 'Security audit',
    text: 'Please share your penetration testing packages.',
    createdAt: '2026-08-21T14:15:00.000Z',
  },
]

/** The Users panel table (demo data - not the real login accounts). */
export const sampleUsers = [
  { name: 'Admin', email: 'admin@novatech.com', role: 'Administrator', status: 'active' },
  { name: 'Jane Cooper', email: 'jane@novatech.com', role: 'Project Manager', status: 'active' },
  { name: 'Ethan Brooks', email: 'ethan@novatech.com', role: 'Frontend Developer', status: 'active' },
  { name: 'Noah Kim', email: 'noah@novatech.com', role: 'Security Engineer', status: 'active' },
  { name: 'Priya Sharma', email: 'priya@novatech.com', role: 'UI/UX Designer', status: 'invited' },
]

/** The Team panel. `initials` are the avatar, as in the original. */
export const teamMembers = [
  { name: 'Michael Carter', role: 'CEO & Co-Founder', initials: 'MC' },
  { name: 'Amira Hassan', role: 'Chief Technology Officer', initials: 'AH' },
  { name: 'Ethan Brooks', role: 'Senior Frontend Developer', initials: 'EB' },
  { name: 'Olivia Nguyen', role: 'Senior Backend Developer', initials: 'ON' },
  { name: 'Priya Sharma', role: 'UI/UX Designer', initials: 'PS' },
  { name: 'Noah Kim', role: 'Cyber Security Engineer', initials: 'NK' },
  { name: 'David Osei', role: 'AI / ML Engineer', initials: 'DO' },
]

/** The Services panel. Note: these are the dashboard's own short blurbs. */
export const dashboardServices = [
  { icon: '\u{1F4BB}', // 💻
    title: 'Web Development',
    desc: 'Fast, responsive websites and web applications.' },
  { icon: '\u{1F4F1}', // 📱
    title: 'Mobile Development',
    desc: 'Native and cross-platform iOS and Android apps.' },
  { icon: '\u{1F916}', // 🤖
    title: 'AI Solutions',
    desc: 'Machine learning, automation and intelligent products.' },
  { icon: '\u{1F512}', // 🔒
    title: 'Cyber Security',
    desc: 'Audits, penetration testing and hardening.' },
  { icon: '\u{2601}', // ☁
    title: 'Cloud Computing',
    desc: 'Scalable architecture, migration and DevOps.' },
  { icon: '\u{1F3A8}', // 🎨
    title: 'UI/UX Design',
    desc: 'Human-centered, premium product interfaces.' },
]

/** Chatbot intents. `status` is 'active' or 'training'. */
export const intents = [
  { icon: '\u{1F44B}', // 👋
    name: 'Greeting',
    desc: 'Welcomes visitors and offers help.',
    status: 'active' },
  { icon: '\u{1F4B0}', // 💰
    name: 'Pricing',
    desc: 'Answers pricing and package questions.',
    status: 'active' },
  { icon: '\u{1F4B8}', // 💸
    name: 'Request Refund',
    desc: 'Handles refund policy enquiries.',
    status: 'training' },
  { icon: '\u{1F4C5}', // 📅
    name: 'Book a Demo',
    desc: 'Schedules product demo calls.',
    status: 'active' },
  { icon: '\u{1F4AC}', // 💬
    name: 'Tech Support',
    desc: 'Escalates issues to human agents.',
    status: 'active' },
  { icon: '\u{1F3E0}', // 🏠
    name: 'Our Services',
    desc: 'Describes NOVA TECH service lines.',
    status: 'training' },
]

/** The three <select> options of the add-project form, plus the status filter. */
export const projectCategories = [
  { value: 'web', label: 'Web' },
  { value: 'mobile', label: 'Mobile' },
  { value: 'ai', label: 'AI' },
  { value: 'security', label: 'Security' },
]

/** The status <select> options, in the original's order. */
export const projectStatuses = [
  { value: 'active', label: 'Active' },
  { value: 'done', label: 'Done' },
  { value: 'pending', label: 'Pending' },
]

/**
 * The sidebar, as title/link groups. `id` doubles as the panel key, the topbar
 * title lookup and the component name, so the three can never get out of sync.
 *
 * The Account group holds the logout button, flagged with `logout: true` - it is
 * an action rather than a panel, but keeping it in the same list means the
 * markup is a single nested v-for.
 */
export const sidebarGroups = [
  {
    title: 'Menu',
    links: [
      { id: 'overview', label: 'Dashboard', icon: '\u{1F4CA}' }, // 📊
      { id: 'projects', label: 'Projects', icon: '\u{1F4C2}' }, // 📂
      { id: 'services', label: 'Services', icon: '\u{1F3E0}' }, // 🏠
      { id: 'team', label: 'Team', icon: '\u{1F465}' }, // 👥
      { id: 'intents', label: 'Intents', icon: '\u{1F916}' }, // 🤖
      { id: 'messages', label: 'Messages', icon: '\u{1F4AC}' }, // 💬
      { id: 'users', label: 'Users', icon: '\u{1F464}' }, // 👤
      { id: 'settings', label: 'Settings', icon: '\u2699' }, // ⚙
    ],
  },
  {
    title: 'Account',
    links: [
      { id: 'logout', label: 'Logout', icon: '\u21A4', logout: true }, // ⇤
    ],
  },
]

/** Maps a panel id to the heading shown in the topbar (panelTitles in the original). */
export const panelTitles = {
  overview: 'Dashboard',
  projects: 'Projects',
  services: 'Services',
  team: 'Team',
  intents: 'Intents',
  messages: 'Messages',
  users: 'Users',
  settings: 'Settings',
}
