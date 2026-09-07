/* ==========================================================================
   services.js
   Drives the Services page:
   - Service data stored as an array of objects
   - Renders service cards with icons, features and prices
   - Live search (keyboard input event)
   - Category filtering (click events)
   - "Learn More" opens a details modal (shared modal from main.js)

   Demonstrates: const, let, arrays, objects, functions, parameters,
   return, if/else, for...of, array methods (filter, map, join, includes,
   toLowerCase), template literals, DOM manipulation, classList,
   addEventListener (click + input), switch.
   ========================================================================== */

// --------------------------------------------------------------------------
// 1. SERVICE DATA (array of objects)
// --------------------------------------------------------------------------
const servicesData = [
  {
    id: 1,
    title: 'Web Development',
    category: 'web',
    icon: 'web',
    description:
      'High-performance websites and web applications built with clean, maintainable code.',
    longDescription:
      'From marketing sites to complex SaaS platforms, we engineer the web with speed, accessibility and SEO in mind. Our stack is chosen for your needs — never for fashion.',
    features: [
      'Custom front-end and back-end development',
      'Responsive, SEO-friendly and accessible builds',
      'CMS integration and e-commerce solutions',
      'API design and third-party integrations',
      'Performance optimization and analytics',
    ],
    startingPrice: 499,
  },
  {
    id: 2,
    title: 'Mobile App Development',
    category: 'mobile',
    icon: 'mobile',
    description:
      'Native and cross-platform mobile apps with smooth, delightful user experiences.',
    longDescription:
      'We design, build and release iOS and Android apps that users love to open. Whether you need a native app or one codebase that runs everywhere, we cover the full lifecycle from store listing to analytics.',
    features: [
      'iOS (Swift) and Android (Kotlin) apps',
      'Cross-platform with React Native / Flutter',
      'Offline-first architecture',
      'Push notifications and deep linking',
      'App Store and Play Store submission',
    ],
    startingPrice: 1499,
  },
  {
    id: 3,
    title: 'AI Solutions',
    category: 'ai',
    icon: 'ai',
    description:
      'Machine learning models and intelligent automation that power smarter decisions.',
    longDescription:
      'Our AI division turns data into products: recommendation engines, computer vision, chatbots and process automation. We start small with proof-of-concepts and scale what proves value.',
    features: [
      'Custom ML model development',
      'Natural language processing & chatbots',
      'Computer vision and image analysis',
      'Predictive analytics dashboards',
      'LLM integration and AI copilots',
    ],
    startingPrice: 2999,
  },
  {
    id: 4,
    title: 'Cyber Security',
    category: 'security',
    icon: 'security',
    description:
      'Proactive audits, penetration testing and protection plans that keep you safe.',
    longDescription:
      'Security is not an add-on — it is a baseline. We audit your infrastructure, test your applications like a real attacker would, and help you fix what matters first.',
    features: [
      'Vulnerability assessments',
      'Penetration testing (web, mobile, network)',
      'Security architecture review',
      'Incident response planning',
      'Compliance support (ISO 27001, GDPR)',
    ],
    startingPrice: 999,
  },
  {
    id: 5,
    title: 'Cloud Computing',
    category: 'cloud',
    icon: 'cloud',
    description:
      'Scalable cloud architecture, migration and DevOps that cut cost and boost speed.',
    longDescription:
      'We architect, migrate and operate cloud environments on AWS, Azure and GCP. From lift-and-shift to full serverless transformations, we make infrastructure boringly reliable.',
    features: [
      'Cloud architecture design',
      'Migration and modernization',
      'CI/CD pipelines and DevOps automation',
      'Kubernetes and container orchestration',
      'Cost optimization and monitoring',
    ],
    startingPrice: 799,
  },
  {
    id: 6,
    title: 'UI/UX Design',
    category: 'design',
    icon: 'design',
    description:
      'Human-centered interfaces and experiences that users love — and that convert.',
    longDescription:
      'We research, prototype and test interfaces until they feel effortless. Every design decision is backed by user behaviour, then handed to engineering as precise, annotated specs.',
    features: [
      'User research and usability testing',
      'Wireframes and interactive prototypes',
      'Design systems and component libraries',
      'Brand-aligned UI design',
      'Design-to-development handoff',
    ],
    startingPrice: 599,
  },
];

// --------------------------------------------------------------------------
// 2. ICON SVG LIBRARY (reusable strings)
// --------------------------------------------------------------------------
const serviceIcons = {
  web: '<svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>',
  mobile: '<svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"></rect><line x1="12" y1="18" x2="12.01" y2="18"></line></svg>',
  ai: '<svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a4 4 0 0 1 4 4 4 4 0 0 1 4 4 4 4 0 0 1-4 4 4 4 0 0 1-4 4 4 4 0 0 1-4-4 4 4 0 0 1-4-4 4 4 0 0 1 4-4 4 4 0 0 1 4-4z"></path></svg>',
  security: '<svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>',
  cloud: '<svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 10h-1.26A8 8 0 1 0 9 20h9a5 5 0 0 0 0-10z"></path></svg>',
  design: '<svg viewBox="0 0 24 24" width="40" height="40" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r="2.5"></circle><circle cx="17.5" cy="10.5" r="2.5"></circle><circle cx="8.5" cy="7.5" r="2.5"></circle><circle cx="6.5" cy="12.5" r="2.5"></circle><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.926 0 1.648-.746 1.648-1.688 0-.437-.18-.835-.437-1.125-.29-.289-.438-.652-.438-1.125a1.64 1.64 0 0 1 1.668-1.668h1.996c3.051 0 5.555-2.503 5.555-5.554C21.965 6.012 17.461 2 12 2z"></path></svg>',
};

// --------------------------------------------------------------------------
// 3. STATE (what the user is currently filtering by)
// --------------------------------------------------------------------------
let activeFilter = 'all';    // "let" because the value changes
const state = { searchTerm: '' }; // object holding live search text

// --------------------------------------------------------------------------
// 4. BOOTSTRAP - only runs on the Services page
// --------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
  // Guard: this script loads on every page, but the grid only exists here
  const grid = document.getElementById('services-grid');
  if (!grid) return;

  renderServices();
  initServiceFilters();
  initServiceSearch();
});

// --------------------------------------------------------------------------
// 5. RENDERING
// --------------------------------------------------------------------------
// Builds one card's HTML. The "service" parameter is an object.
function buildServiceCard(service) {
  // Map over the features array and transform each item into a <li>
  const featuresHtml = service.features
    .map(function (feature) {
      return '<li>' + window.escapeHtml(feature) + '</li>';
    })
    .join('');

  // Template literal with the full card markup
  return `
    <article class="service-card service-card--detail card-enter" data-id="${service.id}">
      <div class="service-card__icon" aria-hidden="true">${serviceIcons[service.icon]}</div>
      <h3 class="service-card__title">${window.escapeHtml(service.title)}</h3>
      <p class="service-card__desc">${window.escapeHtml(service.description)}</p>
      <ul class="service-card__features">${featuresHtml}</ul>
      <span class="price-tag">Starting at $${service.startingPrice.toLocaleString()}</span>
      <button class="service-card__link service-card__learn" data-id="${service.id}">Learn More &rarr;</button>
    </article>
  `;
}

// Renders the currently filtered list into the grid
function renderServices() {
  const grid = document.getElementById('services-grid');
  const emptyBox = document.getElementById('services-empty');
  const countLabel = document.getElementById('service-count');

  // 1) Filter by category
  let filtered = servicesData;

  if (activeFilter !== 'all') {
    // array method: filter() keeps only matching services
    filtered = servicesData.filter(function (service) {
      return service.category === activeFilter;
    });
  }

  // 2) Also filter by the search term (if any was typed)
  if (state.searchTerm !== '') {
    filtered = filtered.filter(matchesSearch);
  }

  // 3) Build all card HTML strings and join them into one big string
  const cardsHtml = filtered.map(buildServiceCard).join('');

  // 4) Put the cards into the DOM
  grid.innerHTML = cardsHtml;

  // 5) Update the result count with a template literal
  countLabel.textContent = `${filtered.length} service${filtered.length === 1 ? '' : 's'} found`;

  // 6) Show the empty state if nothing matched
  if (filtered.length === 0) {
    emptyBox.classList.add('show');
  } else {
    emptyBox.classList.remove('show');
  }

  // 7) Wire up the "Learn More" buttons (event delegation would also work,
  //    but direct binding keeps this beginner-friendly)
  const learnButtons = grid.querySelectorAll('.service-card__learn');
  for (const button of learnButtons) {
    button.addEventListener('click', function () {
      openServiceDetails(Number(button.getAttribute('data-id')));
    });
  }

  // 8) Animate newly rendered cards if the observer is available
  window.observeReveal(grid);
}

// Search predicate: does a service match the current search term?
function matchesSearch(service) {
  const term = state.searchTerm.toLowerCase();

  // Does the title OR description include the term?
  const titleMatch = service.title.toLowerCase().includes(term);
  const descMatch = service.description.toLowerCase().includes(term);

  // Also scan the features list using a for...of loop
  let featureMatch = false;
  for (const feature of service.features) {
    if (feature.toLowerCase().includes(term)) {
      featureMatch = true;
      // Stop scanning once we find one match
      break;
    }
  }

  // return combines the three checks into one boolean
  return titleMatch || descMatch || featureMatch;
}

// --------------------------------------------------------------------------
// 6. CATEGORY FILTERS (click events)
// --------------------------------------------------------------------------
function initServiceFilters() {
  const filterButtons = document.querySelectorAll('#service-filters .filter-btn');

  for (const button of filterButtons) {
    button.addEventListener('click', function () {
      // Read the data-filter attribute from the clicked button
      activeFilter = button.getAttribute('data-filter');

      // Move the .active class to the clicked button (classList)
      for (const otherButton of filterButtons) {
        otherButton.classList.remove('active');
      }
      button.classList.add('active');

      renderServices();
    });
  }
}

// --------------------------------------------------------------------------
// 7. LIVE SEARCH (keyboard input event)
// --------------------------------------------------------------------------
function initServiceSearch() {
  const searchInput = document.getElementById('service-search');

  searchInput.addEventListener('input', function () {
    // Keep the trimmed value in our state object
    state.searchTerm = searchInput.value.trim();
    renderServices();
  });
}

// --------------------------------------------------------------------------
// 8. DETAILS MODAL
//    Uses the shared modal from main.js (window.openModal).
// --------------------------------------------------------------------------
function openServiceDetails(serviceId) {
  // Find the matching object using the array method find().
  // Demonstrates a switch as an alternative form of branching.
  const service = servicesData.find(function (item) {
    return item.id === serviceId;
  });

  if (!service) return;

  // Format the price into the card markup
  const featuresHtml = service.features
    .map(function (feature) {
      return '<li>' + window.escapeHtml(feature) + '</li>';
    })
    .join('');

  // Capitalize the category for display using switch
  let categoryLabel;
  switch (service.category) {
    case 'web':      categoryLabel = 'Web Development'; break;
    case 'mobile':   categoryLabel = 'Mobile Development'; break;
    case 'ai':       categoryLabel = 'AI Solutions'; break;
    case 'security': categoryLabel = 'Cyber Security'; break;
    case 'cloud':    categoryLabel = 'Cloud Computing'; break;
    case 'design':   categoryLabel = 'UI/UX Design'; break;
    default:         categoryLabel = service.category;
  }

  // Template literal builds the modal content
  const modalHtml = `
    <div class="modal-detail">
      <span class="modal-detail__category">${categoryLabel}</span>
      <h3>${window.escapeHtml(service.title)}</h3>
      <p>${window.escapeHtml(service.longDescription)}</p>
      <h4>What's included</h4>
      <ul class="modal-detail__features">${featuresHtml}</ul>
      <div class="price-tag">Starting at $${service.startingPrice.toLocaleString()}</div>
      <div>
        <a href="contact.html" class="btn btn--primary">Request A Quote</a>
      </div>
    </div>
  `;

  window.openModal(modalHtml);
}