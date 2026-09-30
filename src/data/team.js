/**
 * The team roster - 10 members, copied verbatim from the original
 * js/team.js `teamData` array.
 *
 * Fields:
 *   id          number   stable key for v-for
 *   name        string
 *   role        string
 *   department  string   executive | engineering | design | security | ai
 *   initials    string   shown instead of a photo (the project has no images)
 *   bio         string
 *   skills      string[] rendered as chips in the modal
 *   social      object   { linkedin, twitter, github }
 *
 * NOTE: every social value is the placeholder '#' in the original data. That
 * is preserved faithfully - these are dead links until real URLs are added.
 */
export const team = [
  {
    id: 1,
    name: 'Michael Carter',
    role: 'CEO & Co-Founder',
    department: 'executive',
    initials: 'MC',
    bio: 'Michael founded NOVA TECH in 2015 and has led the company from a two-person studio to a global team of 50+. He spends his weeks with clients, not spreadsheets.',
    skills: ['Leadership', 'Business Strategy', 'Client Relations', 'Product Vision'],
    social: { linkedin: '#', twitter: '#', github: '#' },
  },
  {
    id: 2,
    name: 'Amira Hassan',
    role: 'Chief Technology Officer',
    department: 'executive',
    initials: 'AH',
    bio: 'Amira sets the technical direction and architecture standards across every NOVA TECH division. She is obsessed with systems that stay fast and simple as they grow.',
    skills: ['System Architecture', 'Cloud', 'AI Strategy', 'Mentoring'],
    social: { linkedin: '#', twitter: '#', github: '#' },
  },
  {
    id: 3,
    name: 'Daniel Reed',
    role: 'VP of Engineering',
    department: 'executive',
    initials: 'DR',
    bio: 'Daniel owns delivery quality for web, mobile, AI and cloud teams. He introduced the review culture and CI pipelines that keep projects on time.',
    skills: ['Engineering Management', 'DevOps', 'Code Review', 'Agile'],
    social: { linkedin: '#', twitter: '#', github: '#' },
  },
  {
    id: 4,
    name: 'Ethan Brooks',
    role: 'Senior Frontend Developer',
    department: 'engineering',
    initials: 'EB',
    bio: 'Ethan turns designs into fast, accessible interfaces. He cares deeply about performance budgets and the small details users feel but never notice.',
    skills: ['HTML5', 'CSS3', 'JavaScript', 'Accessibility', 'Web Performance'],
    social: { linkedin: '#', twitter: '#', github: '#' },
  },
  {
    id: 5,
    name: 'Olivia Nguyen',
    role: 'Senior Backend Developer',
    department: 'engineering',
    initials: 'ON',
    bio: 'Olivia builds the APIs and databases that power our products. She loves clean schemas, reliable queues and APIs that other developers enjoy using.',
    skills: ['Node.js', 'PostgreSQL', 'Redis', 'API Design', 'Docker'],
    social: { linkedin: '#', twitter: '#', github: '#' },
  },
  {
    id: 6,
    name: 'Priya Sharma',
    role: 'UI/UX Designer',
    department: 'design',
    initials: 'PS',
    bio: 'Priya leads the design system that keeps every NOVA TECH product consistent and premium. She prototypes, tests and refines until the flow feels effortless.',
    skills: ['Figma', 'Prototyping', 'User Research', 'Design Systems'],
    social: { linkedin: '#', twitter: '#', github: '#' },
  },
  {
    id: 7,
    name: 'Lucas Silva',
    role: 'Full-Stack Developer',
    department: 'engineering',
    initials: 'LS',
    bio: 'Lucas ships features end to end, from the database to the button on screen. He is the developer who makes the middle of every sprint look easy.',
    skills: ['JavaScript', 'React Native', 'Node.js', 'MongoDB', 'GraphQL'],
    social: { linkedin: '#', twitter: '#', github: '#' },
  },
  {
    id: 8,
    name: 'Noah Kim',
    role: 'Cyber Security Engineer',
    department: 'security',
    initials: 'NK',
    bio: "Noah tests our clients' systems the way a real attacker would — then helps fix them. He runs the penetration testing and hardening practice.",
    skills: ['Penetration Testing', 'Network Security', 'Cryptography', 'Incident Response'],
    social: { linkedin: '#', twitter: '#', github: '#' },
  },
  {
    id: 9,
    name: 'Emma Wilson',
    role: 'Security Analyst',
    department: 'security',
    initials: 'EW',
    bio: 'Emma monitors threats around the clock and turns security findings into plain-language action plans that teams can actually follow.',
    skills: ['Threat Monitoring', 'SIEM', 'Security Audits', 'Compliance'],
    social: { linkedin: '#', twitter: '#', github: '#' },
  },
  {
    id: 10,
    name: 'David Osei',
    role: 'AI / Machine Learning Engineer',
    department: 'ai',
    initials: 'DO',
    bio: 'David trains the models behind our AI products — from chatbots to computer vision. He is careful about data quality and honest about model limits.',
    skills: ['Python', 'TensorFlow', 'PyTorch', 'Computer Vision', 'NLP'],
    social: { linkedin: '#', twitter: '#', github: '#' },
  },
]

/**
 * The six member IDs shown on the homepage "featured team" grid.
 * This is an ID list, NOT a slice - ids 1,4,5,6,8,10 are not the first six.
 */
export const featuredIds = [1, 4, 5, 6, 8, 10]

/**
 * Resolves featuredIds into member objects, preserving the ID order.
 * Replaces the for...of + find() loop from the original renderFeaturedTeam().
 */
export const featuredMembers = featuredIds
  .map((id) => team.find((member) => member.id === id))
  .filter(Boolean)

/** The 6 department chips on the team page filter bar. */
export const teamFilters = [
  { value: 'all', label: 'All' },
  { value: 'executive', label: 'Executive' },
  { value: 'engineering', label: 'Engineering' },
  { value: 'design', label: 'Design' },
  { value: 'security', label: 'Security' },
  { value: 'ai', label: 'AI' },
]

/**
 * Maps social keys to display names. The original built this object inside
 * openMemberModal(); lifting it into the data file lets the modal template
 * iterate over it with v-for.
 */
export const socialLabels = {
  linkedin: 'LinkedIn',
  twitter: 'Twitter',
  github: 'GitHub',
}
