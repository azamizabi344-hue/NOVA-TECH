/* ==========================================================================
   team.js
   Serves TWO pages:
   1. Homepage: fills "#featured-team" with 6 featured members.
   2. Team page: fills "#team-grid" with all 10 members and adds live search,
      department filtering and a full profile modal.

   Demonstrates: const, let, arrays of objects, functions, parameters,
   return, if/else, for...of, for...in, array methods (filter, map, find,
   includes, toLowerCase), template literals, DOM manipulation,
   addEventListener, classList, string concatenation vs template literals.
   ========================================================================== */

// --------------------------------------------------------------------------
// 1. TEAM DATA (array of 10 employee objects)
// --------------------------------------------------------------------------
const teamData = [
  {
    id: 1,
    name: 'Michael Carter',
    role: 'CEO & Co-Founder',
    department: 'executive',
    initials: 'MC',
    bio: 'Michael founded NOVA TECH in 2015 and has led the company from a two-person studio to a global team of 50+. He spends his weeks with clients, not spreadsheets.',
    skills: ['Leadership', 'Business Strategy', 'Client Relations', 'Product Vision'],
    social: {
      linkedin: '#',
      twitter: '#',
      github: '#',
    },
  },
  {
    id: 2,
    name: 'Amira Hassan',
    role: 'Chief Technology Officer',
    department: 'executive',
    initials: 'AH',
    bio: 'Amira sets the technical direction and architecture standards across every NOVA TECH division. She is obsessed with systems that stay fast and simple as they grow.',
    skills: ['System Architecture', 'Cloud', 'AI Strategy', 'Mentoring'],
    social: {
      linkedin: '#',
      twitter: '#',
      github: '#',
    },
  },
  {
    id: 3,
    name: 'Daniel Reed',
    role: 'VP of Engineering',
    department: 'executive',
    initials: 'DR',
    bio: 'Daniel owns delivery quality for web, mobile, AI and cloud teams. He introduced the review culture and CI pipelines that keep projects on time.',
    skills: ['Engineering Management', 'DevOps', 'Code Review', 'Agile'],
    social: {
      linkedin: '#',
      twitter: '#',
      github: '#',
    },
  },
  {
    id: 4,
    name: 'Ethan Brooks',
    role: 'Senior Frontend Developer',
    department: 'engineering',
    initials: 'EB',
    bio: 'Ethan turns designs into fast, accessible interfaces. He cares deeply about performance budgets and the small details users feel but never notice.',
    skills: ['HTML5', 'CSS3', 'JavaScript', 'Accessibility', 'Web Performance'],
    social: {
      linkedin: '#',
      twitter: '#',
      github: '#',
    },
  },
  {
    id: 5,
    name: 'Olivia Nguyen',
    role: 'Senior Backend Developer',
    department: 'engineering',
    initials: 'ON',
    bio: 'Olivia builds the APIs and databases that power our products. She loves clean schemas, reliable queues and APIs that other developers enjoy using.',
    skills: ['Node.js', 'PostgreSQL', 'Redis', 'API Design', 'Docker'],
    social: {
      linkedin: '#',
      twitter: '#',
      github: '#',
    },
  },
  {
    id: 6,
    name: 'Priya Sharma',
    role: 'UI/UX Designer',
    department: 'design',
    initials: 'PS',
    bio: 'Priya leads the design system that keeps every NOVA TECH product consistent and premium. She prototypes, tests and refines until the flow feels effortless.',
    skills: ['Figma', 'Prototyping', 'User Research', 'Design Systems'],
    social: {
      linkedin: '#',
      twitter: '#',
      github: '#',
    },
  },
  {
    id: 7,
    name: 'Lucas Silva',
    role: 'Full-Stack Developer',
    department: 'engineering',
    initials: 'LS',
    bio: 'Lucas ships features end to end, from the database to the button on screen. He is the developer who makes the middle of every sprint look easy.',
    skills: ['JavaScript', 'React Native', 'Node.js', 'MongoDB', 'GraphQL'],
    social: {
      linkedin: '#',
      twitter: '#',
      github: '#',
    },
  },
  {
    id: 8,
    name: 'Noah Kim',
    role: 'Cyber Security Engineer',
    department: 'security',
    initials: 'NK',
    bio: 'Noah tests our clients\' systems the way a real attacker would — then helps fix them. He runs the penetration testing and hardening practice.',
    skills: ['Penetration Testing', 'Network Security', 'Cryptography', 'Incident Response'],
    social: {
      linkedin: '#',
      twitter: '#',
      github: '#',
    },
  },
  {
    id: 9,
    name: 'Emma Wilson',
    role: 'Security Analyst',
    department: 'security',
    initials: 'EW',
    bio: 'Emma monitors threats around the clock and turns security findings into plain-language action plans that teams can actually follow.',
    skills: ['Threat Monitoring', 'SIEM', 'Security Audits', 'Compliance'],
    social: {
      linkedin: '#',
      twitter: '#',
      github: '#',
    },
  },
  {
    id: 10,
    name: 'David Osei',
    role: 'AI / Machine Learning Engineer',
    department: 'ai',
    initials: 'DO',
    bio: 'David trains the models behind our AI products — from chatbots to computer vision. He is careful about data quality and honest about model limits.',
    skills: ['Python', 'TensorFlow', 'PyTorch', 'Computer Vision', 'NLP'],
    social: {
      linkedin: '#',
      twitter: '#',
      github: '#',
    },
  },
];

// IDs of the six members featured on the homepage team section
const featuredIds = [1, 4, 5, 6, 8, 10];

// --------------------------------------------------------------------------
// 2. HELPERS
// --------------------------------------------------------------------------
function capitalize(word) {
  if (word.length === 0) return word;
  return word.charAt(0).toUpperCase() + word.slice(1);
}

// Builds one employee card as an HTML string
function buildTeamCard(member) {
  return `
    <article class="team-card card-enter">
      <div class="team-card__photo">${window.escapeHtml(member.initials)}</div>
      <div class="team-card__info">
        <span class="team-card__badge">${capitalize(member.department)}</span>
        <h3 class="team-card__name">${window.escapeHtml(member.name)}</h3>
        <div class="team-card__role">${window.escapeHtml(member.role)}</div>
        <p class="team-card__bio">${window.escapeHtml(member.bio)}</p>
        <button class="team-card__btn" data-id="${member.id}">View Profile &rarr;</button>
      </div>
    </article>
  `;
}

// Wires the "View Profile" buttons inside a grid to the modal
function bindTeamButtons(grid) {
  const buttons = grid.querySelectorAll('.team-card__btn');

  for (const button of buttons) {
    button.addEventListener('click', function () {
      const memberId = Number(button.getAttribute('data-id'));
      openMemberModal(memberId);
    });
  }
}

// Renders a list of members into a grid element
function renderTeamList(members, grid, emptyBox, countLabel) {
  const cardsHtml = members.map(buildTeamCard).join('');
  grid.innerHTML = cardsHtml;

  countLabel.textContent =
    members.length + ' team member' + (members.length === 1 ? '' : 's') + ' found';

  if (members.length === 0) {
    emptyBox.classList.add('show');
  } else {
    emptyBox.classList.remove('show');
  }

  bindTeamButtons(grid);
  window.observeReveal(grid);
}

// --------------------------------------------------------------------------
// 3. HOMEPAGE "FEATURED TEAM"
// --------------------------------------------------------------------------
function renderFeaturedTeam() {
  const grid = document.getElementById('featured-team');
  if (!grid) return;

  // for...of loop to collect only the featured members in order
  const featuredMembers = [];
  for (const id of featuredIds) {
    // array method: find() locates the member by id
    const member = teamData.find(function (m) {
      return m.id === id;
    });
    if (member) {
      featuredMembers.push(member);
    }
  }

  grid.innerHTML = featuredMembers.map(buildTeamCard).join('');
  bindTeamButtons(grid);
  window.observeReveal(grid);
}

// --------------------------------------------------------------------------
// 4. FULL TEAM PAGE (search + department filter)
// --------------------------------------------------------------------------
// Mutable state for the team page controls
const teamState = { search: '', filter: 'all' };

function initTeamPage() {
  const grid = document.getElementById('team-grid');
  const emptyBox = document.getElementById('team-empty');
  const countLabel = document.getElementById('team-count');
  const searchInput = document.getElementById('team-search');
  const filterBar = document.getElementById('team-filters');

  // Initial render
  renderFilteredTeam();

  // a) Department filter buttons (click events)
  const filterButtons = filterBar.querySelectorAll('.filter-btn');
  for (const button of filterButtons) {
    button.addEventListener('click', function () {
      teamState.filter = button.getAttribute('data-filter');

      for (const other of filterButtons) other.classList.remove('active');
      button.classList.add('active');

      renderFilteredTeam();
    });
  }

  // b) Live search (input event)
  searchInput.addEventListener('input', function () {
    teamState.search = searchInput.value.trim();
    renderFilteredTeam();
  });

  // Applies filter + search and renders
  function renderFilteredTeam() {
    let filtered = teamData;

    if (teamState.filter !== 'all') {
      filtered = teamData.filter(function (m) {
        return m.department === teamState.filter;
      });
    }

    if (teamState.search !== '') {
      filtered = filtered.filter(matchesMemberSearch);
    }

    renderTeamList(filtered, grid, emptyBox, countLabel);
  }
}

// Checks a member against the current search term
function matchesMemberSearch(member) {
  const term = teamState.search.toLowerCase();

  const nameMatch = member.name.toLowerCase().includes(term);
  const roleMatch = member.role.toLowerCase().includes(term);
  const depMatch = member.department.toLowerCase().includes(term);

  // Loop over the skills array with a for...of loop
  let skillMatch = false;
  for (const skill of member.skills) {
    if (skill.toLowerCase().includes(term)) {
      skillMatch = true;
      break;
    }
  }

  return nameMatch || roleMatch || depMatch || skillMatch;
}

// --------------------------------------------------------------------------
// 5. MEMBER PROFILE MODAL
// --------------------------------------------------------------------------
function openMemberModal(memberId) {
  const member = teamData.find(function (m) {
    return m.id === memberId;
  });

  if (!member) return;

  // Skills as chips
  const skillsHtml = member.skills
    .map(function (skill) {
      return '<span>' + window.escapeHtml(skill) + '</span>';
    })
    .join('');

  // Social links (for...in over the social object)
  // We build an array of { key, label } first so we can control display names.
  const socialLabels = { linkedin: 'LinkedIn', twitter: 'Twitter', github: 'GitHub' };

  let socialsHtml = '';
  for (const key in member.social) {
    // Skip inherited properties — only own properties appear here,
    // but checking is good practice with for...in.
    if (Object.prototype.hasOwnProperty.call(member.social, key)) {
      socialsHtml +=
        '<a href="' + window.escapeHtml(member.social[key]) + '" aria-label="' + key + '">' +
        socialLabels[key] + '</a>';
    }
  }

  const modalHtml = `
    <div class="modal-detail">
      <span class="modal-detail__category">${capitalize(member.department)}</span>
      <h3>${window.escapeHtml(member.name)}</h3>
      <div class="modal-detail__meta">
        <span><strong>Position:</strong> ${window.escapeHtml(member.role)}</span>
      </div>
      <p>${window.escapeHtml(member.bio)}</p>
      <h4>Skills</h4>
      <div class="skill-chips">${skillsHtml}</div>
      <div class="modal-detail__socials">${socialsHtml}</div>
    </div>
  `;

  window.openModal(modalHtml);
}

// --------------------------------------------------------------------------
// 6. BOOTSTRAP
// --------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
  // Homepage has "#featured-team", team page has "#team-grid"
  if (document.getElementById('featured-team')) {
    renderFeaturedTeam();
    return;
  }

  initTeamPage();
});