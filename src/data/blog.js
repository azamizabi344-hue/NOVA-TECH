/**
 * The article catalogue - 10 posts, copied verbatim from the original
 * js/blog.js `articlesData` array.
 *
 * Fields:
 *   id              number  stable key for v-for
 *   title           string
 *   category        string  web | mobile | ai | security | cloud | company
 *   imageText       string  the big gradient label (PERFORMANCE, LLM, ...)
 *   author          string
 *   authorInitials  string  the round avatar next to the name
 *   date            string  a pre-formatted display date, NOT an ISO string
 *   readTime        number  minutes
 *   description     string  the one-liner on the card
 *   content         string  the paragraph shown in the modal
 *
 * NOTE on `date`: it is stored as an already-formatted string like
 * 'Feb 12, 2026', exactly as the original did. That is convenient for display
 * and useless for sorting - to sort by date you would need real Date objects.
 * The original never sorted, so this is faithful rather than a bug.
 */
export const articles = [
  {
    id: 1,
    title: '10 Web Performance Tips For 2026',
    category: 'web',
    imageText: 'PERFORMANCE',
    author: 'Ethan Brooks',
    authorInitials: 'EB',
    date: 'Feb 12, 2026',
    readTime: 6,
    description:
      'Slow pages cost money. Here are the ten performance habits we apply to every single project.',
    content:
      'Performance is a feature. In this article we walk through real-world improvements: honest image sizing, avoiding render-blocking scripts, caching strategies, predictable server responses and measuring everything with real-user metrics. The good news is that most sites are slow for the same ten reasons, so fixing those gives you 90% of the win.',
  },
  {
    id: 2,
    title: 'How We Ship Mobile Apps Faster',
    category: 'mobile',
    imageText: 'MOBILE',
    author: 'Olivia Nguyen',
    authorInitials: 'ON',
    date: 'Jan 28, 2026',
    readTime: 5,
    description:
      "Our mobile team's playbook for landing releases on time without burning people out.",
    content:
      'Fast releases are not about working harder — they are about smaller batches, predictable release trains and automated testing where it matters. We share the tooling, the release checklist and the decisions that keep quality high while the calendar stays realistic.',
  },
  {
    id: 3,
    title: 'Building Trustworthy AI Products',
    category: 'ai',
    imageText: 'AI',
    author: 'David Osei',
    authorInitials: 'DO',
    date: 'Jan 10, 2026',
    readTime: 8,
    description:
      'Helpful AI needs transparency, guardrails and honest limits. Here is how we approach it.',
    content:
      'Trust is the invisible feature of great AI products. We discuss model evaluation before launch, slow rollout strategies, human-in-the-loop reviews and, most importantly, being honest with users about what the model can and cannot do.',
  },
  {
    id: 4,
    title: 'The Security Checklist Every Startup Needs',
    category: 'security',
    imageText: 'SECURITY',
    author: 'Noah Kim',
    authorInitials: 'NK',
    date: 'Dec 18, 2025',
    readTime: 7,
    description:
      'You do not need a security department to ship safely — you need a checklist. Start here.',
    content:
      'Most startups share the same security gaps: unmanaged secrets, missing backups, open ports and lazy password policies. This checklist ranks the highest-impact fixes you can make this week, in plain language, with no vendor bias.',
  },
  {
    id: 5,
    title: 'Choosing The Right Cloud For Your Startup',
    category: 'cloud',
    imageText: 'CLOUD',
    author: 'Amira Hassan',
    authorInitials: 'AH',
    date: 'Dec 2, 2025',
    readTime: 6,
    description:
      'AWS, Azure or GCP? The answer is rarely about free credits. Here is how we choose.',
    content:
      "Cloud choice should follow your team's strengths, your compliance needs and your workload shape — not the biggest discount. We break down the decision into factors you can actually evaluate on day one, plus a few traps to avoid.",
  },
  {
    id: 6,
    title: 'Inside Our Design System',
    category: 'web',
    imageText: 'DESIGN SYS',
    author: 'Priya Sharma',
    authorInitials: 'PS',
    date: 'Nov 15, 2025',
    readTime: 5,
    description:
      'How one shared language of colors, type and components keeps every product consistent.',
    content:
      'A design system is a contract between designers and engineers. We show how our tokens map to real components, how we version them, and how teams ship faster once the boring parts are decided once.',
  },
  {
    id: 7,
    title: 'From Two Laptops To 50 Engineers: Our Story',
    category: 'company',
    imageText: 'STORY',
    author: 'Michael Carter',
    authorInitials: 'MC',
    date: 'Oct 30, 2025',
    readTime: 9,
    description:
      'A decade of NOVA TECH in one post: the wins, the pivots and the lessons.',
    content:
      'Ten years ago we were two people and one desk. This is the honest version of the journey — where we got lucky, where we got it wrong and the handful of principles we would never trade for a bigger office.',
  },
  {
    id: 8,
    title: 'Demystifying Penetration Testing',
    category: 'security',
    imageText: 'PENTEST',
    author: 'Emma Wilson',
    authorInitials: 'EW',
    date: 'Oct 12, 2025',
    readTime: 6,
    description:
      'What actually happens during a pen test — and why the report is the valuable part.',
    content:
      'A penetration test is a time-boxed, authorised attempt to break in. We explain the stages, the rules, and why the final report matters more than the "exploits found" headline. Preparation turns a scary process into a gift for your security.',
  },
  {
    id: 9,
    title: 'Making Accessibility A Feature, Not A Fix',
    category: 'web',
    imageText: 'A11Y',
    author: 'Ethan Brooks',
    authorInitials: 'EB',
    date: 'Sep 25, 2025',
    readTime: 6,
    description:
      'Accessibility is cheaper and easier when it is planned from the first commit.',
    content:
      'Keyboard navigation, sensible focus states, real heading structure and honest alt text cost almost nothing at the start of a project. We show a practical list you can add to every sprint from day one.',
  },
  {
    id: 10,
    title: 'What LLMs Changed About Software Development',
    category: 'ai',
    imageText: 'LLMS',
    author: 'David Osei',
    authorInitials: 'DO',
    date: 'Sep 8, 2025',
    readTime: 7,
    description:
      "LLMs change how we write code, not why we write it. Two engineers' honest take.",
    content:
      'AI assistants are great at velocity and dangerous at confidence. We talk about where they genuinely save us hours — boilerplate, tests, docs — and where a human review step is non-negotiable before anything reaches production.',
  },
]

/** The 7 category chips on the blog page filter bar. */
export const blogFilters = [
  { value: 'all', label: 'All' },
  { value: 'web', label: 'Web' },
  { value: 'mobile', label: 'Mobile' },
  { value: 'ai', label: 'AI' },
  { value: 'security', label: 'Security' },
  { value: 'cloud', label: 'Cloud' },
  { value: 'company', label: 'Company' },
]
