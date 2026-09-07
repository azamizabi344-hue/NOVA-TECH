/* ==========================================================================
   dashboard.js
   Drives the admin dashboard after a successful login.

   Features:
   - Auth guard: redirects to login.html if there is no session
   - Sidebar navigation (switch panels, update the topbar title)
   - Overview cards (projects/messages update live as data changes)
   - Recent projects table + recent messages list
   - Project management (add, update status, delete projects)
   - Services, Team, Messages, Users and Settings panels
   - Sessions/users persisted in localStorage (demo only)

   Demonstrates: const, let, arrays, objects, functions, parameters,
   return, if/else, switch, for...of, while, array methods (filter, map,
   find, slice, join, unshift, push), template literals, DOM manipulation,
   addEventListener (click, submit, change, input), classList, localStorage
   + JSON, Date, try/catch.
   ========================================================================== */

// --------------------------------------------------------------------------
// 1. DATA (seed data + localStorage persistence)
// --------------------------------------------------------------------------
const STORAGE_KEY = 'novatech_dashboard_projects';
const DISPLAY_NAME_KEY = 'novatech_display_name';

// Seed project list (the project management panel starts with these)
const initialProjects = [
  { id: 1, name: 'BrightCart Commerce Platform', category: 'web', client: 'BrightCart Inc.', year: 2025, status: 'done' },
  { id: 2, name: 'Sentra AI Support Assistant', category: 'ai', client: 'Sentra Corp', year: 2025, status: 'active' },
  { id: 3, name: 'OrbitRide Ride-Hailing App', category: 'mobile', client: 'OrbitRide', year: 2025, status: 'active' },
  { id: 4, name: 'SecureBank Fraud Shield', category: 'security', client: 'SecureBank', year: 2025, status: 'done' },
  { id: 5, name: 'MediTrust Patient Portal', category: 'web', client: 'MediTrust Health', year: 2024, status: 'done' },
  { id: 6, name: 'FitPulse Fitness Tracker', category: 'mobile', client: 'FitPulse', year: 2024, status: 'pending' },
];

// Base stats shown on the overview cards
const BASE_STATS = {
  projects: 150,
  users: 1248,
  team: 50,
  intents: 12,
  revenue: '$2.4M',
};

// Sample messages used when no contact-form messages exist yet
const sampleMessages = [
  { name: 'Sarah Mitchell', email: 'sarah@brightcart.com', subject: 'E-commerce replatform', text: 'We would love a quote for rebuilding our storefront.', createdAt: '2026-09-02T10:00:00.000Z' },
  { name: 'James Okafor', email: 'james@finlytics.com', subject: 'AI roadmap', text: 'Interested in a chatbot and predictive analytics pilot.', createdAt: '2026-08-28T09:30:00.000Z' },
  { name: 'Laura Chen', email: 'laura@meditrust.com', subject: 'Security audit', text: 'Please share your penetration testing packages.', createdAt: '2026-08-21T14:15:00.000Z' },
];

// Sample user accounts for the Users panel
const sampleUsers = [
  { name: 'Admin', email: 'admin@novatech.com', role: 'Administrator', status: 'active' },
  { name: 'Jane Cooper', email: 'jane@novatech.com', role: 'Project Manager', status: 'active' },
  { name: 'Ethan Brooks', email: 'ethan@novatech.com', role: 'Frontend Developer', status: 'active' },
  { name: 'Noah Kim', email: 'noah@novatech.com', role: 'Security Engineer', status: 'active' },
  { name: 'Priya Sharma', email: 'priya@novatech.com', role: 'UI/UX Designer', status: 'invited' },
];

// Team members for the Team panel
const teamMembers = [
  { name: 'Michael Carter', role: 'CEO & Co-Founder', initials: 'MC' },
  { name: 'Amira Hassan', role: 'Chief Technology Officer', initials: 'AH' },
  { name: 'Ethan Brooks', role: 'Senior Frontend Developer', initials: 'EB' },
  { name: 'Olivia Nguyen', role: 'Senior Backend Developer', initials: 'ON' },
  { name: 'Priya Sharma', role: 'UI/UX Designer', initials: 'PS' },
  { name: 'Noah Kim', role: 'Cyber Security Engineer', initials: 'NK' },
  { name: 'David Osei', role: 'AI / ML Engineer', initials: 'DO' },
];

// Services for the Services panel
const servicesList = [
  { icon: '&#128187;', title: 'Web Development', desc: 'Fast, responsive websites and web applications.' },
  { icon: '&#128241;', title: 'Mobile Development', desc: 'Native and cross-platform iOS and Android apps.' },
  { icon: '&#129504;', title: 'AI Solutions', desc: 'Machine learning, automation and intelligent products.' },
  { icon: '&#128274;', title: 'Cyber Security', desc: 'Audits, penetration testing and hardening.' },
  { icon: '&#9729;', title: 'Cloud Computing', desc: 'Scalable architecture, migration and DevOps.' },
  { icon: '&#127912;', title: 'UI/UX Design', desc: 'Human-centered, premium product interfaces.' },
];

// Sample conversational intents for the Intents panel
const intentsList = [
  { icon: '&#128075;', name: 'Greeting', desc: 'Welcomes visitors and offers help.', status: 'active' },
  { icon: '&#128176;', name: 'Pricing', desc: 'Answers pricing and package questions.', status: 'active' },
  { icon: '&#128184;', name: 'Request Refund', desc: 'Handles refund policy enquiries.', status: 'training' },
  { icon: '&#128197;', name: 'Book a Demo', desc: 'Schedules product demo calls.', status: 'active' },
  { icon: '&#128172;', name: 'Tech Support', desc: 'Escalates issues to human agents.', status: 'active' },
  { icon: '&#128736;', name: 'Our Services', desc: 'Describes NOVA TECH service lines.', status: 'training' },
];

// Load projects from localStorage, or fall back to the seed list
function loadProjects() {
  try {
    const raw = localStorage.getItem(STORAGE_KEY);
    const data = JSON.parse(raw);
    if (Array.isArray(data)) return data;
    return initialProjects.slice();
  } catch (error) {
    console.error('Could not load projects:', error);
    return initialProjects.slice();
  }
}

// Keep a mutable copy of the project list
let projects = loadProjects();

// Persist the current project list
function saveProjects() {
  try {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(projects));
  } catch (error) {
    console.error('Could not save projects:', error);
  }
}

// --------------------------------------------------------------------------
// 2. BOOTSTRAP — guard + init
// --------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
  const user = window.auth.getUser();

  // Auth guard: no session? kick the visitor back to the login page
  if (!user) {
    window.location.href = 'login.html';
    return;
  }

  initDashboard(user);
});

function initDashboard(user) {
  // Topbar user info
  document.getElementById('user-email').textContent = user.email;
  document.getElementById('user-avatar').textContent = user.name.charAt(0).toUpperCase();

  // Sidebar / topbar logout buttons
  document.getElementById('sidebar-logout').addEventListener('click', function () {
    window.auth.logout();
  });
  document.getElementById('topbar-logout').addEventListener('click', function () {
    window.auth.logout();
  });

  // Sidebar navigation
  initSidebarNav();

  // Render every panel
  renderOverview();
  renderManageProjects();
  renderServicesPanel();
  renderTeamPanel();
  renderIntentsPanel();
  renderMessagesPanel();
  renderUsersPanel();
  initProjectForm();
  initSettingsForm(user);
}

// --------------------------------------------------------------------------
// 3. SIDEBAR NAVIGATION
// --------------------------------------------------------------------------
const panelTitles = {
  overview: 'Dashboard',
  projects: 'Projects',
  services: 'Services',
  team: 'Team',
  intents: 'Intents',
  messages: 'Messages',
  users: 'Users',
  settings: 'Settings',
};

function initSidebarNav() {
  const navLinks = document.querySelectorAll('.sidebar__link[data-panel]');
  const panels = document.querySelectorAll('.panel');

  for (const link of navLinks) {
    link.addEventListener('click', function () {
      const panelName = link.getAttribute('data-panel');

      // Move the .active class across the sidebar links
      for (const other of navLinks) other.classList.remove('active');
      link.classList.add('active');

      // Show only the matching panel
      for (const panel of panels) panel.classList.remove('active');
      document.getElementById('panel-' + panelName).classList.add('active');

      // Update the topbar heading using the map above
      document.getElementById('topbar-title').textContent = panelTitles[panelName];
    });
  }
}

// --------------------------------------------------------------------------
// 4. OVERVIEW PANEL (cards + recent rows)
// --------------------------------------------------------------------------
function renderOverview() {
  // Total projects = company base count + whatever was added/deleted here
  const addedCount = projects.length - initialProjects.length;
  const totalProjects = BASE_STATS.projects + addedCount;

  const allMessages = getAllMessages();

  document.getElementById('card-projects').textContent = totalProjects;
  document.getElementById('card-users').textContent = BASE_STATS.users;
  document.getElementById('card-messages').textContent = allMessages.length;
  document.getElementById('card-team').textContent = BASE_STATS.team;
  document.getElementById('card-intents').textContent = BASE_STATS.intents;
  document.getElementById('card-revenue').textContent = BASE_STATS.revenue;

  renderRecentProjectsTable();
  renderRecentMessagesList();
}

// Recent projects: newest 5
function renderRecentProjectsTable() {
  const tbody = document.getElementById('recent-projects-body');

  const recent = projects.slice(0, 5);
  const rowsHtml = recent.map(buildCompactRow).join('');

  tbody.innerHTML = rowsHtml;
}

function buildCompactRow(project) {
  return `
    <tr>
      <td><strong>${window.escapeHtml(project.name)}</strong></td>
      <td>${capitalize(project.category)}</td>
      <td>${window.escapeHtml(project.client)}</td>
      <td>${project.year}</td>
      <td><span class="badge badge--${project.status}">${capitalize(project.status)}</span></td>
    </tr>
  `;
}

// Recent messages: newest 3
function renderRecentMessagesList() {
  const list = document.getElementById('recent-messages-list');
  const recent = getAllMessages().slice(0, 3);
  list.innerHTML = recent.map(buildMessageItem).join('');
}

// --------------------------------------------------------------------------
// 5. PROJECT MANAGEMENT PANEL
// --------------------------------------------------------------------------
function renderManageProjects() {
  const tbody = document.getElementById('manage-projects-body');
  const rowsHtml = projects.map(buildManageRow).join('');
  tbody.innerHTML = rowsHtml;

  // Bind status change events
  const statusSelects = tbody.querySelectorAll('.dash-status-select');
  for (const select of statusSelects) {
    select.addEventListener('change', function () {
      const projectId = Number(select.getAttribute('data-id'));
      const project = projects.find(function (p) { return p.id === projectId; });
      if (project) {
        project.status = select.value;
        saveProjects();
        renderOverview(); // keep overview + cards in sync
      }
      renderManageProjects(); // re-render to reflect the new badge
    });
  }

  // Bind delete events
  const deleteButtons = tbody.querySelectorAll('.dash-delete');
  for (const button of deleteButtons) {
    button.addEventListener('click', function () {
      const projectId = Number(button.getAttribute('data-id'));
      // array method: filter() removes the matching project
      projects = projects.filter(function (p) { return p.id !== projectId; });
      saveProjects();
      renderOverview();
      renderManageProjects();
    });
  }
}

function buildManageRow(project) {
  // Build the options markup with the current status preselected
  const statusOptions = ['active', 'done', 'pending'];
  const optionsHtml = statusOptions
    .map(function (status) {
      const selected = project.status === status ? ' selected' : '';
      return `<option value="${status}"${selected}>${capitalize(status)}</option>`;
    })
    .join('');

  return `
    <tr data-id="${project.id}">
      <td><strong>${window.escapeHtml(project.name)}</strong></td>
      <td>${capitalize(project.category)}</td>
      <td>${window.escapeHtml(project.client)}</td>
      <td>${project.year}</td>
      <td>
        <select class="sort-select dash-status-select" data-id="${project.id}" aria-label="Status">
          ${optionsHtml}
        </select>
      </td>
      <td>
        <button class="btn btn--ghost btn--sm dash-delete" data-id="${project.id}">Delete</button>
      </td>
    </tr>
  `;
}

// Add-project form
function initProjectForm() {
  const form = document.getElementById('add-project-form');
  const successBox = document.getElementById('add-project-success');

  form.addEventListener('submit', function (event) {
    event.preventDefault();

    const name = document.getElementById('ap-name').value.trim();
    const category = document.getElementById('ap-category').value;
    const client = document.getElementById('ap-client').value.trim();
    const year = Number(document.getElementById('ap-year').value);
    const status = document.getElementById('ap-status').value;

    // Validate the name and year fields
    if (name === '') {
      alert('Please enter a project name.');
      return;
    }
    if (!year || year < 2015 || year > 2030) {
      alert('Please enter a valid year between 2015 and 2030.');
      return;
    }

    // Create the new project object and add it to the FRONT of the list
    const newProject = {
      id: Date.now(), // unique-ish id from the Date object
      name: name,
      category: category,
      client: client || 'Unknown client',
      year: year,
      status: status,
    };

    projects.unshift(newProject);
    saveProjects();

    // Refresh the UI
    renderOverview();
    renderManageProjects();

    // Reset the form and show a quick success message
    form.reset();
    successBox.classList.add('show');
    setTimeout(function () {
      successBox.classList.remove('show');
    }, 4000);
  });
}

// --------------------------------------------------------------------------
// 6. OTHER PANELS (services, team, messages, users, settings)
// --------------------------------------------------------------------------
function renderServicesPanel() {
  const grid = document.getElementById('dash-services-grid');
  const cardsHtml = servicesList
    .map(function (service) {
      return `
        <div class="service-card">
          <div class="service-card__icon" aria-hidden="true">${service.icon}</div>
          <h3 class="service-card__title">${service.title}</h3>
          <p class="service-card__desc">${service.desc}</p>
        </div>
      `;
    })
    .join('');
  grid.innerHTML = cardsHtml;
}

function renderTeamPanel() {
  const grid = document.getElementById('dash-team-grid');
  const cardsHtml = teamMembers
    .map(function (member) {
      return `
        <div class="dash-team__member">
          <span class="dash-team__avatar">${window.escapeHtml(member.initials)}</span>
          <div>
            <h4>${window.escapeHtml(member.name)}</h4>
            <p>${window.escapeHtml(member.role)}</p>
          </div>
        </div>
      `;
    })
    .join('');
  grid.innerHTML = cardsHtml;
}

function renderIntentsPanel() {
  const grid = document.getElementById('dash-intents-grid');
  const cardsHtml = intentsList
    .map(function (intent) {
      return `
        <div class="dash-intents__item">
          <span class="dash-intents__icon" aria-hidden="true">${intent.icon}</span>
          <div>
            <h4>${window.escapeHtml(intent.name)}</h4>
            <p>${window.escapeHtml(intent.desc)}</p>
            <span class="badge ${intent.status === 'active' ? 'badge--active' : 'badge--pending'}">${capitalize(intent.status)}</span>
          </div>
        </div>
      `;
    })
    .join('');
  grid.innerHTML = cardsHtml;
}

function renderMessagesPanel() {
  const list = document.getElementById('messages-list');
  list.innerHTML = getAllMessages().map(buildMessageItem).join('');
}

function buildMessageItem(message) {
  // Format the ISO date into something readable with the Date object
  let dateLabel;
  try {
    const parsed = new Date(message.createdAt);
    dateLabel = parsed.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
  } catch (error) {
    dateLabel = 'Unknown date';
  }

  return `
    <div class="message-list__item">
      <div>
        <div class="message-list__info">
          <strong>${window.escapeHtml(message.name)} (${window.escapeHtml(message.email)})</strong>
          <span>${dateLabel} &middot; ${window.escapeHtml(message.subject)}</span>
        </div>
        <p class="message-list__text">${window.escapeHtml(message.text)}</p>
      </div>
    </div>
  `;
}

function renderUsersPanel() {
  const tbody = document.getElementById('users-table-body');
  const rowsHtml = sampleUsers
    .map(function (user) {
      const badgeClass = user.status === 'active' ? 'badge--active' : 'badge--pending';
      return `
        <tr>
          <td><strong>${window.escapeHtml(user.name)}</strong></td>
          <td>${window.escapeHtml(user.email)}</td>
          <td>${window.escapeHtml(user.role)}</td>
          <td><span class="badge ${badgeClass}">${capitalize(user.status)}</span></td>
        </tr>
      `;
    })
    .join('');
  tbody.innerHTML = rowsHtml;
}

function initSettingsForm(user) {
  // Prefill from storage or the session user
  const displayName = localStorage.getItem(DISPLAY_NAME_KEY) || user.name;

  document.getElementById('set-name').value = displayName;
  document.getElementById('set-email').value = user.email;
  document.getElementById('set-role').value = user.role;

  const form = document.getElementById('settings-form');
  const successBox = document.getElementById('settings-success');

  form.addEventListener('submit', function (event) {
    event.preventDefault();

    const newName = document.getElementById('set-name').value.trim();

    if (newName === '') {
      alert('Display name cannot be empty.');
      return;
    }

    try {
      localStorage.setItem(DISPLAY_NAME_KEY, newName);
      console.log('Display name saved:', newName);
    } catch (error) {
      console.error('Could not save display name:', error);
    }

    successBox.classList.add('show');
    setTimeout(function () {
      successBox.classList.remove('show');
    }, 4000);
  });
}

// --------------------------------------------------------------------------
// 7. SHARED HELPERS
// --------------------------------------------------------------------------
function capitalize(word) {
  if (word.length === 0) return word;
  return word.charAt(0).toUpperCase() + word.slice(1);
}

// Merges contact-form messages (from localStorage) with sample data
function getAllMessages() {
  let contactMessages = [];
  try {
    const raw = localStorage.getItem('novatech_contact_messages');
    const parsed = JSON.parse(raw);
    if (Array.isArray(parsed)) {
      contactMessages = parsed;
    }
  } catch (error) {
    console.error('Could not load contact messages:', error);
  }

  // If visitors have submitted messages, prefer those; otherwise use samples
  if (contactMessages.length > 0) {
    return contactMessages;
  }
  return sampleMessages;
}