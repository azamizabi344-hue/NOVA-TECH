/* ==========================================================================
   blog.js
   Drives the Blog page:
   - 10 article objects (title, author, date, category, description, content)
   - Renders article cards with a gradient "image" placeholder
   - Live search (title, description, author, category)
   - Category filtering (click events)
   - "Read More" opens a full-article modal

   Demonstrates: const, let, arrays of objects, functions, parameters,
   return, else if, for...of, array methods (filter, map, join, includes,
   find, toLowerCase), template literals, DOM manipulation, addEventListener,
   classList, Date (string dates stored and displayed).
   ========================================================================== */

// --------------------------------------------------------------------------
// 1. ARTICLE DATA (array of 10 objects)
// --------------------------------------------------------------------------
const articlesData = [
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
      'Our mobile team\'s playbook for landing releases on time without burning people out.',
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
      'Cloud choice should follow your team\'s strengths, your compliance needs and your workload shape — not the biggest discount. We break down the decision into factors you can actually evaluate on day one, plus a few traps to avoid.',
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
      'LLMs change how we write code, not why we write it. Two engineers\' honest take.',
    content:
      'AI assistants are great at velocity and dangerous at confidence. We talk about where they genuinely save us hours — boilerplate, tests, docs — and where a human review step is non-negotiable before anything reaches production.',
  },
];

// --------------------------------------------------------------------------
// 2. HELPERS
// --------------------------------------------------------------------------
function capitalize(word) {
  if (word.length === 0) return word;
  return word.charAt(0).toUpperCase() + word.slice(1);
}

// Builds one article card as an HTML string
function buildArticleCard(article) {
  return `
    <article class="blog-card card-enter">
      <div class="blog-card__image">
        <span>${window.escapeHtml(article.imageText)}</span>
        <span class="blog-card__category">${capitalize(article.category)}</span>
      </div>
      <div class="blog-card__body">
        <h3 class="blog-card__title">${window.escapeHtml(article.title)}</h3>
        <div class="blog-card__meta">
          <span class="blog-card__author">
            <span class="blog-card__avatar">${window.escapeHtml(article.authorInitials)}</span>
            ${window.escapeHtml(article.author)}
          </span>
          <span>&bull; ${window.escapeHtml(article.date)}</span>
          <span>&bull; ${article.readTime} min read</span>
        </div>
        <p class="blog-card__desc">${window.escapeHtml(article.description)}</p>
        <button class="blog-card__btn" data-id="${article.id}">Read More &rarr;</button>
      </div>
    </article>
  `;
}

// Wires the "Read More" buttons inside the grid to the modal
function bindArticleButtons(grid) {
  const buttons = grid.querySelectorAll('.blog-card__btn');

  for (const button of buttons) {
    button.addEventListener('click', function () {
      const articleId = Number(button.getAttribute('data-id'));
      openArticleModal(articleId);
    });
  }
}

// Renders the current list into the grid and updates the UI
function renderFilteredArticles() {
  const grid = document.getElementById('blog-grid');
  const emptyBox = document.getElementById('blog-empty');
  const countLabel = document.getElementById('blog-count');

  // 1) Filter by category
  let filtered = articlesData;
  if (blogState.filter !== 'all') {
    filtered = articlesData.filter(function (article) {
      return article.category === blogState.filter;
    });
  }

  // 2) Filter by search term
  if (blogState.search !== '') {
    filtered = filtered.filter(matchesArticleSearch);
  }

  // 3) Build and inject the HTML
  const cardsHtml = filtered.map(buildArticleCard).join('');
  grid.innerHTML = cardsHtml;

  // 4) Update the count label
  countLabel.textContent =
    filtered.length + ' article' + (filtered.length === 1 ? '' : 's') + ' found';

  // 5) Toggle the empty state
  if (filtered.length === 0) {
    emptyBox.classList.add('show');
  } else {
    emptyBox.classList.remove('show');
  }

  bindArticleButtons(grid);
  window.observeReveal(grid);
}

// Search predicate: does an article match the current term?
function matchesArticleSearch(article) {
  const term = blogState.search.toLowerCase();

  const titleMatch = article.title.toLowerCase().includes(term);
  const descMatch = article.description.toLowerCase().includes(term);
  const authorMatch = article.author.toLowerCase().includes(term);
  const categoryMatch = article.category.toLowerCase().includes(term);

  return titleMatch || descMatch || authorMatch || categoryMatch;
}

// --------------------------------------------------------------------------
// 3. STATE + BOOTSTRAP
// --------------------------------------------------------------------------
// Mutable state for the blog page controls
const blogState = { search: '', filter: 'all' };

document.addEventListener('DOMContentLoaded', function () {
  const grid = document.getElementById('blog-grid');
  if (!grid) return; // guard: only the blog page has this grid

  // Initial render
  renderFilteredArticles();

  // a) Category filter buttons (click events)
  const filterButtons = document.querySelectorAll('#blog-filters .filter-btn');
  for (const button of filterButtons) {
    button.addEventListener('click', function () {
      blogState.filter = button.getAttribute('data-filter');

      for (const other of filterButtons) other.classList.remove('active');
      button.classList.add('active');

      renderFilteredArticles();
    });
  }

  // b) Live search (input event)
  const searchInput = document.getElementById('blog-search');
  searchInput.addEventListener('input', function () {
    blogState.search = searchInput.value.trim();
    renderFilteredArticles();
  });
});

// --------------------------------------------------------------------------
// 4. ARTICLE MODAL
//    Uses the shared modal from main.js (window.openModal).
// --------------------------------------------------------------------------
function openArticleModal(articleId) {
  // array method: find() returns the first matching article
  const article = articlesData.find(function (a) {
    return a.id === articleId;
  });

  if (!article) return;

  const modalHtml = `
    <div class="modal-detail">
      <span class="modal-detail__category">${capitalize(article.category)}</span>
      <h3>${window.escapeHtml(article.title)}</h3>
      <div class="modal-detail__meta">
        <span><strong>Author:</strong> ${window.escapeHtml(article.author)}</span>
        <span><strong>Date:</strong> ${window.escapeHtml(article.date)}</span>
        <span><strong>Read:</strong> ${article.readTime} min</span>
      </div>
      <p>${window.escapeHtml(article.content)}</p>
      <div>
        <a href="contact.html" class="btn btn--primary">Discuss This Topic</a>
      </div>
    </div>
  `;

  window.openModal(modalHtml);
}