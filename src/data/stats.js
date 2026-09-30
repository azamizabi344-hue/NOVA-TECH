/**
 * The company statistics.
 *
 * The original project had these as literal HTML:
 *   <span class="stat-card__number counter" data-target="150">0</span>
 *   <span class="stat-card__suffix">+</span>
 *   <h4 class="stat-card__label">Projects Completed</h4>
 *
 * Two different sets existed - one on the homepage, one on the about page -
 * both feeding the same CSS. Both are kept here, unchanged.
 */

/** Homepage stats section (#statistics). */
export const homeStats = [
  { id: 'projects', value: 150, suffix: '+', label: 'Projects Completed' },
  { id: 'team', value: 50, suffix: '+', label: 'Team Members' },
  { id: 'countries', value: 30, suffix: '+', label: 'Countries' },
  { id: 'satisfaction', value: 98, suffix: '%', label: 'Client Satisfaction' },
]

/**
 * The three numbers in the hero block. They repeat the first three homepage
 * stats, which is why the original hardcoded them twice.
 */
export const heroStats = [
  { id: 'projects', value: 150, suffix: '+', label: 'Projects Delivered' },
  { id: 'team', value: 50, suffix: '+', label: 'Team Members' },
  { id: 'countries', value: 30, suffix: '+', label: 'Countries Served' },
]

/** About page stats - identical styling, different data (6 years, not 98%). */
export const aboutStats = [
  { id: 'projects', value: 150, suffix: '+', label: 'Projects Completed' },
  { id: 'team', value: 50, suffix: '+', label: 'Team Members' },
  { id: 'countries', value: 30, suffix: '+', label: 'Countries' },
  { id: 'years', value: 6, suffix: '', label: 'Years of Experience' },
]
