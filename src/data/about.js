/**
 * Content for the About page.
 *
 * The original about.html was 343 lines of static HTML with NO about.js at all -
 * the page was pure markup, and the only dynamic part was the four counters,
 * which main.js animated via a global document.querySelectorAll('.counter').
 *
 * Everything below is that markup, turned into data.
 */

/** The "From Two Developers To A Global Team" story block. */
export const aboutStory = {
  visualLabel: 'Our Journey',
  tag: 'Our Story',
  title: 'From Two Developers To A Global Team',
  paragraphs: [
    'NOVA TECH was founded in 2015 in a small shared office with just two laptops and a single mission: prove that small teams can ship software large companies would be proud of.',
    'Ten years later we are a 50+ person company with engineers, designers, data scientists and security specialists across 30+ countries. We have delivered over 150 projects — from startup MVPs to enterprise platforms — while keeping the same hands-on, quality-first mindset we started with.',
  ],
}

/**
 * Mission & Vision - three cards.
 *
 * `icon` holds an emoji. They are written as \u{...} escapes rather than
 * literal characters so the file can never be corrupted by an editor or a
 * copy/paste that guesses the wrong encoding. The comment shows the glyph.
 */
export const missionCards = [
  {
    id: 'mission',
    icon: '\u{1F3AF}', // 🎯 target
    title: 'Our Mission',
    text: 'Empower businesses of every size with accessible, reliable and innovative technology that solves real problems — delivered on time and on budget.',
  },
  {
    id: 'vision',
    icon: '\u{1F576}\u{FE0F}', // 🕶️ sunglasses (FE0F = variation selector)
    title: 'Our Vision',
    text: 'A world where every company can harness modern technology to grow, compete and create value — regardless of its size or budget.',
  },
  {
    id: 'approach',
    icon: '\u{1F91D}', // 🤝 handshake
    title: 'Our Approach',
    text: 'We pair senior expertise with deep listening. Every engagement starts with understanding your goals, then we engineer the simplest solution that reaches them.',
  },
]

/** Core Values - four cards. */
export const coreValues = [
  {
    id: 'innovation',
    icon: '\u{2728}', // ✨ sparkles
    title: 'Innovation',
    text: 'We explore new tools and ideas before they become trends.',
  },
  {
    id: 'integrity',
    icon: '\u{1F512}', // 🔒 lock
    title: 'Integrity',
    text: 'We are transparent with our process, pricing and timelines.',
  },
  {
    id: 'collaboration',
    icon: '\u{1F91D}', // 🤝 handshake
    title: 'Collaboration',
    text: 'We treat our clients as partners, not tickets to close.',
  },
  {
    id: 'excellence',
    icon: '\u{1F3C5}\u{FE0F}', // 🏅 medal (FE0F = variation selector)
    title: 'Excellence',
    text: 'We sweat the details, because the details become the product.',
  },
]

/** The "Our Journey So Far" timeline. `year` is a string: the last one is "Today". */
export const timeline = [
  {
    id: 2015,
    year: '2015',
    title: 'NOVA TECH is founded',
    desc: 'Two developers begin building websites in a shared office.',
  },
  {
    id: 2017,
    year: '2017',
    title: 'Mobile development launch',
    desc: 'We ship our first iOS and Android apps and pass 50 clients.',
  },
  {
    id: 2019,
    year: '2019',
    title: 'AI & Data division',
    desc: 'A dedicated team starts building machine-learning products.',
  },
  {
    id: 2020,
    year: '2020',
    title: 'Cyber security practice',
    desc: 'Security audits and penetration testing become core services.',
  },
  {
    id: 2022,
    year: '2022',
    title: 'Global cloud scale',
    desc: 'Cloud solutions across 30+ countries and multi-region deployments.',
  },
  {
    id: 'today',
    year: 'Today',
    title: '150+ projects delivered',
    desc: 'A 50+ person team serving clients worldwide with 98% satisfaction.',
  },
]

/**
 * Leadership - four cards.
 *
 * NOTE THE INCONSISTENCY, which is faithfully preserved from the original:
 * the fourth person here is "Sophie Zhang, Head of Design", and that name
 * appears NOWHERE else in the project. The team roster (src/data/team.js) has
 * no Sophie Zhang; its only design-department member is Priya Sharma, listed
 * as "UI/UX Designer" - a different person with a different role.
 *
 * So the original project shipped two contradictory versions of who leads
 * design. This is a content bug in the source, not a conversion error, so it
 * has been left exactly as-is rather than quietly "fixed". If you want them
 * reconciled, the options are:
 *   a) change this entry to Priya Sharma + the team.js role, or
 *   b) add Sophie Zhang to src/data/team.js and the original js/team.js.
 *
 * Note also the initials are SINGLE letters here (M, A, D, S) whereas the team
 * page uses two (MC, AH, DR, PS). That difference is also original.
 */
export const leadership = [
  {
    id: 'michael-carter',
    initials: 'M',
    name: 'Michael Carter',
    role: 'CEO & Co-Founder',
    text: 'Sets the company vision and loves talking with clients about bold product ideas.',
  },
  {
    id: 'amira-hassan',
    initials: 'A',
    name: 'Amira Hassan',
    role: 'Chief Technology Officer',
    text: 'Leads engineering strategy, architecture and technical hiring worldwide.',
  },
  {
    id: 'daniel-reed',
    initials: 'D',
    name: 'Daniel Reed',
    role: 'VP of Engineering',
    text: 'Owns delivery quality across web, mobile, AI and cloud teams.',
  },
  {
    id: 'sophie-zhang',
    initials: 'S',
    name: 'Sophie Zhang',
    role: 'Head of Design',
    text: 'Leads the design system and makes sure everything we ship feels premium.',
  },
]
