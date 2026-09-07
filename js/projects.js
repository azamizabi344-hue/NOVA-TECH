/* ==========================================================================
   projects.js
   Serves TWO pages:
   1. Homepage: fills "#featured-projects" (6 featured cards) and wires the
      All/Web/Mobile/AI/Security filter bar.
   2. Projects page: fills "#projects-grid" with all 12 projects and adds
      live search, category filtering, sorting and a details modal.

   Demonstrates: const, let, arrays of objects, functions, parameters,
   return, if/else, switch, for...of, while, array methods (filter, map,
   find, sort, slice, join, includes, toLowerCase, localeCompare), template
   literals, DOM manipulation, addEventListener (click, input, change),
   classList, JSON (data stored as literal objects).
   ========================================================================== */

// --------------------------------------------------------------------------
// 1. PROJECT DATA (array of 12 project objects)
// --------------------------------------------------------------------------
const projectsData = [
  {
    id: 1,
    name: 'BrightCart Commerce Platform',
    category: 'web',
    imageText: 'BRIGHT',
    description: 'A blazing-fast e-commerce platform rebuilt from a slow legacy monolith.',
    longDescription:
      'We modernized BrightCart\'s entire online store, introduced a modular architecture and cut page load times by 60%, which directly lifted conversion rates across their catalog.',
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
      'We hardened SecureBank\'s transaction flow with behavioural analytics and anomaly detection, blocking fraudulent activity while keeping false positives low.',
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
];

// How many projects to show on the homepage "Featured" section
const FEATURED_LIMIT = 6;

// --------------------------------------------------------------------------
// 2. HELPERS
// --------------------------------------------------------------------------
// Capitalizes the first letter of a string (used for category labels)
function capitalize(word) {
  if (word.length === 0) return word;
  return word.charAt(0).toUpperCase() + word.slice(1);
}

// Builds the chip markup for an array of technologies
function buildTechChips(techArray) {
  // array method: map() transforms each item, join() merges into one string
  const chips = techArray.map(function (tech) {
    return '<span class="project-card__tech-item">' + window.escapeHtml(tech) + '</span>';
  });
  return chips.join('');
}

// Builds one project card as an HTML string
function buildProjectCard(project) {
  return `
    <article class="project-card card-enter" data-category="${project.category}">
      <div class="project-card__image">
        <span class="project-card__label">${window.escapeHtml(project.imageText)}</span>
        <span class="project-card__category">${capitalize(project.category)}</span>
      </div>
      <div class="project-card__body">
        <h3 class="project-card__title">${window.escapeHtml(project.name)}</h3>
        <p class="project-card__desc">${window.escapeHtml(project.description)}</p>
        <div class="project-card__tech">${buildTechChips(project.technologies)}</div>
        <button class="project-card__btn" data-id="${project.id}">View Details &rarr;</button>
      </div>
    </article>
  `;
}

// Wires the "View Details" buttons inside a grid to the modal
function bindProjectButtons(grid) {
  const buttons = grid.querySelectorAll('.project-card__btn');

  for (const button of buttons) {
    button.addEventListener('click', function () {
      const projectId = Number(button.getAttribute('data-id'));
      openProjectModal(projectId);
    });
  }
}

// Renders a list of project objects into a grid element
function renderProjectList(projects, grid, emptyBox, countLabel) {
  const cardsHtml = projects.map(buildProjectCard).join('');
  grid.innerHTML = cardsHtml;

  countLabel.textContent =
    projects.length + ' project' + (projects.length === 1 ? '' : 's') + ' found';

  // Show or hide the empty state depending on the result count
  if (projects.length === 0) {
    emptyBox.classList.add('show');
  } else {
    emptyBox.classList.remove('show');
  }

  bindProjectButtons(grid);
  window.observeReveal(grid);
}

// --------------------------------------------------------------------------
// 3. HOMEPAGE "FEATURED PROJECTS"
// --------------------------------------------------------------------------
function initFeaturedProjects() {
  const grid = document.getElementById('featured-projects');
  const filterBar = document.getElementById('project-filters');

  // Local state for the homepage filter
  let featuredFilter = 'all';

  renderFeatured();

  // Wire the filter buttons on the homepage
  const filterButtons = filterBar.querySelectorAll('.filter-btn');
  for (const button of filterButtons) {
    button.addEventListener('click', function () {
      featuredFilter = button.getAttribute('data-filter');

      for (const other of filterButtons) other.classList.remove('active');
      button.classList.add('active');

      renderFeatured();
    });
  }

  function renderFeatured() {
    // Filter the full dataset, then slice the first 6
    let featured = projectsData;
    if (featuredFilter !== 'all') {
      featured = projectsData.filter(function (p) {
        return p.category === featuredFilter;
      });
    }
    featured = featured.slice(0, FEATURED_LIMIT);

    grid.innerHTML = featured.map(buildProjectCard).join('');
    bindProjectButtons(grid);
    window.observeReveal(grid);
  }
}

// --------------------------------------------------------------------------
// 4. FULL PROJECTS PAGE (search + filter + sort)
// --------------------------------------------------------------------------
// Mutable state for the projects page controls
let projectSort = 'newest';
const projectState = { search: '', filter: 'all' };

function initProjectsPage() {
  const grid = document.getElementById('projects-grid');
  const emptyBox = document.getElementById('projects-empty');
  const countLabel = document.getElementById('project-count');
  const searchInput = document.getElementById('project-search');
  const sortSelect = document.getElementById('project-sort');
  const filterBar = document.getElementById('project-filters');

  // Initial render
  renderFilteredProjects();

  // a) Category filter buttons (click events)
  const filterButtons = filterBar.querySelectorAll('.filter-btn');
  for (const button of filterButtons) {
    button.addEventListener('click', function () {
      projectState.filter = button.getAttribute('data-filter');

      for (const other of filterButtons) other.classList.remove('active');
      button.classList.add('active');

      renderFilteredProjects();
    });
  }

  // b) Live search (input event)
  searchInput.addEventListener('input', function () {
    projectState.search = searchInput.value.trim();
    renderFilteredProjects();
  });

  // c) Sort dropdown (change event)
  sortSelect.addEventListener('change', function () {
    projectSort = sortSelect.value;
    renderFilteredProjects();
  });

  // Applies filter + search, then sorts and renders
  function renderFilteredProjects() {
    // Step 1: filter by category
    let filtered = projectsData;
    if (projectState.filter !== 'all') {
      filtered = projectsData.filter(function (p) {
        return p.category === projectState.filter;
      });
    }

    // Step 2: filter by search term
    if (projectState.search !== '') {
      filtered = filtered.filter(matchesProjectSearch);
    }

    // Step 3: sort (slice() copies first so sort() does not mutate the source array)
    const sorted = filtered.slice().sort(compareProjects);

    // Step 4: render
    renderProjectList(sorted, grid, emptyBox, countLabel);
  }
}

// Checks a project against the current search term
function matchesProjectSearch(project) {
  const term = projectState.search.toLowerCase();

  const nameMatch = project.name.toLowerCase().includes(term);
  const descMatch = project.description.toLowerCase().includes(term);
  const clientMatch = project.client.toLowerCase().includes(term);

  // Check every technology using a while loop (a bit unusual here,
  // included to demonstrate while loops)
  let techMatch = false;
  let index = 0;
  while (index < project.technologies.length) {
    if (project.technologies[index].toLowerCase().includes(term)) {
      techMatch = true;
      break; // exit the loop early on the first match
    }
    index++;
  }

  return nameMatch || descMatch || clientMatch || techMatch;
}

// Comparator used by sort(). Returns a number for the sort algorithm.
function compareProjects(a, b) {
  switch (projectSort) {
    case 'newest':
      return b.year - a.year;
    case 'oldest':
      return a.year - b.year;
    case 'az':
      return a.name.localeCompare(b.name);
    default:
      return 0;
  }
}

// --------------------------------------------------------------------------
// 5. PROJECT DETAILS MODAL
// --------------------------------------------------------------------------
function openProjectModal(projectId) {
  // array method: find() returns the first matching object
  const project = projectsData.find(function (p) {
    return p.id === projectId;
  });

  if (!project) return;

  // Tech chips (bigger variant for the modal)
  const techChips = project.technologies
    .map(function (tech) {
      return '<span>' + window.escapeHtml(tech) + '</span>';
    })
    .join('');

  const modalHtml = `
    <div class="modal-detail">
      <span class="modal-detail__category">${capitalize(project.category)}</span>
      <h3>${window.escapeHtml(project.name)}</h3>
      <div class="modal-detail__meta">
        <span><strong>Client:</strong> ${window.escapeHtml(project.client)}</span>
        <span><strong>Year:</strong> ${project.year}</span>
      </div>
      <p>${window.escapeHtml(project.longDescription)}</p>
      <h4>Technologies</h4>
      <div class="tech-chips">${techChips}</div>
      <div>
        <a href="contact.html" class="btn btn--primary">Start A Similar Project</a>
      </div>
    </div>
  `;

  window.openModal(modalHtml);
}

// --------------------------------------------------------------------------
// 6. BOOTSTRAP
//    Runs on every page, but only acts if the matching elements exist.
// --------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
  // Homepage has "#featured-projects", projects page has "#projects-grid"
  const featuredGrid = document.getElementById('featured-projects');

  if (featuredGrid) {
    initFeaturedProjects();
    return; // early return: we are on the homepage
  }

  initProjectsPage();
});