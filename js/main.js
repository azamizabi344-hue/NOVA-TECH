/* ==========================================================================
   main.js
   Shared logic used across every page of the NOVA TECH site:

   - Footer copyright year (uses the Date object)
   - Animated number counters (150+, 50+, 30+, 98%)
   - Scroll reveal animations (IntersectionObserver)
   - FAQ accordion
   - Newsletter form validation + localStorage
   - Shared modal helpers: openModal / closeModal / escapeHtml
     (reused by projects.js, team.js, blog.js and services.js)

   This file defines global helpers on `window` so the page-specific
   scripts (loaded after this one) can call them.
   ========================================================================== */

// ------------------------------------------------------------
// 1. KICK OFF EVERYTHING ONCE THE DOCUMENT IS READY
// ------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
  initFooterYear();
  initCounters();
  initScrollReveal();
  initFaq();
  initNewsletter();
});

// ------------------------------------------------------------
// 2. FOOTER YEAR
//    Demonstrates the Date object.
// ------------------------------------------------------------
function initFooterYear() {
  const yearElement = document.querySelector('#footer-year');

  if (yearElement) {
    // Store the current year in a const variable
    const currentYear = new Date().getFullYear();
    yearElement.textContent = currentYear;
  }
}

// ------------------------------------------------------------
// 3. ANIMATED COUNTERS
//    Counts up from 0 to the number stored in data-target when
//    the element scrolls into view.
// ------------------------------------------------------------
function initCounters() {
  // querySelectorAll returns a NodeList of every ".counter" element
  const counters = document.querySelectorAll('.counter');

  // Nothing to animate on this page? Stop early.
  if (counters.length === 0) return;

  // An IntersectionObserver fires once each counter becomes visible
  const observer = new IntersectionObserver(function (entries) {
    for (const entry of entries) {
      if (entry.isIntersecting) {
        animateCounter(entry.target);
        // Stop watching once it has animated
        observer.unobserve(entry.target);
      }
    }
  }, { threshold: 0.4 });

  // Start watching every counter element
  for (const counter of counters) {
    observer.observe(counter);
  }
}

// Smoothly counts an element up to its data-target value
function animateCounter(element) {
  // Convert the string attribute into a number
  const target = Number(element.getAttribute('data-target'));
  const duration = 2000;
  const startTime = performance.now();

  // update() is called on every animation frame (about 60x per second)
  function update(currentTime) {
    // How far through the animation are we? Between 0 and 1.
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);

    // Ease the progress (eases out so the end feels natural)
    const eased = 1 - Math.pow(1 - progress, 3);

    // Write the rounded number into the element
    element.textContent = Math.floor(eased * target);

    // Keep animating until progress reaches 1
    if (progress < 1) {
      requestAnimationFrame(update);
    } else {
      // Finish exactly on the target number
      element.textContent = target;
    }
  }

  requestAnimationFrame(update);
}

// ------------------------------------------------------------
// 4. SCROLL REVEAL
//    Elements with the "reveal" class fade/slide up when visible.
//    Exposed as window.observeReveal so JS-rendered cards
//    (projects, team, blog, services) can animate too.
// ------------------------------------------------------------
let revealObserver = null;

function createRevealObserver() {
  revealObserver = new IntersectionObserver(function (entries) {
    for (const entry of entries) {
      if (entry.isIntersecting) {
        // Flip the element to visible
        entry.target.classList.add('revealed');
        // No need to keep watching it
        revealObserver.unobserve(entry.target);
      }
    }
  }, { threshold: 0.15 });
}

// Observes every .reveal element, optionally scoped to a container
window.observeReveal = function (scope) {
  if (!revealObserver) {
    createRevealObserver();
  }

  // Use the provided scope or fall back to the whole document
  const root = scope || document;
  const elements = root.querySelectorAll('.reveal:not(.revealed)');

  // Register each element (a classic for...of loop)
  for (const element of elements) {
    revealObserver.observe(element);
  }
};

// Adds reveal classes to static homepage sections, then observes them
function initScrollReveal() {
  // Tag the heading blocks so they rise in
  const sectionHeads = document.querySelectorAll('.section-head');
  for (const head of sectionHeads) {
    head.classList.add('reveal');
  }

  // Tag grid children with a subtle stagger
  const gridGroups = document.querySelectorAll('.services__grid, .pricing__grid, .testimonials__grid');

  for (const group of gridGroups) {
    // Loop using an index-based for loop so we can stagger the delay
    for (let i = 0; i < group.children.length; i++) {
      group.children[i].classList.add('reveal');
      // First three cards get increasing delay classes (1s, 2s, 3s)
      if (i < 3) {
        group.children[i].classList.add('reveal-delay-' + (i + 1));
      }
    }
  }

  // Start watching everything
  window.observeReveal(document);
}

// ------------------------------------------------------------
// 5. FAQ ACCORDION
//    Clicking a question opens or closes its answer. Only one
//    item stays open at a time.
// ------------------------------------------------------------
function initFaq() {
  const faqItems = document.querySelectorAll('.faq-item');

  if (faqItems.length === 0) return;

  for (const item of faqItems) {
    const question = item.querySelector('.faq-item__question');

    question.addEventListener('click', function () {
      const wasOpen = item.classList.contains('open');

      // Close every item first
      closeAllFaqItems();

      // If it was closed, open it now (if/else logic)
      if (wasOpen) {
        item.classList.remove('open');
        question.setAttribute('aria-expanded', 'false');
      } else {
        item.classList.add('open');
        question.setAttribute('aria-expanded', 'true');
      }
    });
  }
}

// Helper that closes every FAQ item at once
function closeAllFaqItems() {
  const faqItems = document.querySelectorAll('.faq-item');

  for (const item of faqItems) {
    item.classList.remove('open');

    const question = item.querySelector('.faq-item__question');
    question.setAttribute('aria-expanded', 'false');
  }
}

// ------------------------------------------------------------
// 6. NEWSLETTER FORM
//    Validates the email, shows a success/error message and
//    saves the subscription into localStorage (demo only).
// ------------------------------------------------------------
function initNewsletter() {
  const form = document.querySelector('#newsletter-form');
  const emailInput = document.querySelector('#newsletter-email');
  const message = document.querySelector('#newsletter-message');

  // This section only exists on the homepage - guard against missing markup
  if (!form || !emailInput || !message) return;

  // Form event: runs when the Subscribe button is clicked
  form.addEventListener('submit', function (event) {
    // Stop the page from refreshing
    event.preventDefault();

    // Trim whitespace from the typed value
    const email = emailInput.value.trim();

    // Use else-if to check conditions in order
    if (!email) {
      showNewsMessage('Please enter your email address.', 'error');
    } else if (!isValidEmail(email)) {
      showNewsMessage('Please enter a valid email address.', 'error');
    } else {
      // Success path: save the email and thank the user
      saveNewsletterEmail(email);

      showNewsMessage('Thanks for subscribing! Stay tuned for updates.', 'success');
      form.reset();
    }
  });

  // Keyboard event: pressing Enter submits the form (default behavior),
  // we just log it here to demonstrate keydown handling.
  emailInput.addEventListener('keydown', function (event) {
    if (event.key === 'Enter') {
      console.log('Newsletter form submitted via Enter key');
    }
  });

  // Shows the feedback message under the form for a few seconds.
  // Nested inside initNewsletter so it can "close over" the message element.
  function showNewsMessage(text, type) {
    message.textContent = text;

    // Class changes the text color: .success (green) or .error (red)
    message.className = 'newsletter__message ' + type;
    message.hidden = false;

    // Hide the message again after a short delay
    setTimeout(function () {
      message.hidden = true;
    }, 4000);
  }
}

// Simple email format check using a regular expression
function isValidEmail(email) {
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailPattern.test(email);
}

// Stores an array of email objects in localStorage.
// Wrapped in try/catch to demonstrate basic error handling.
function saveNewsletterEmail(email) {
  try {
    const storageKey = 'novatech_newsletter';
    const rawData = localStorage.getItem(storageKey);

    // Parse existing data; default to an empty array if none exists
    let subscriptions = JSON.parse(rawData);

    if (!Array.isArray(subscriptions)) {
      subscriptions = [];
    }

    // Push a new object onto the array
    subscriptions.push({
      email: email,
      date: new Date().toISOString(),
    });

    // Serialize back to JSON and store
    localStorage.setItem(storageKey, JSON.stringify(subscriptions));

    return true;
  } catch (error) {
    // Reached only if localStorage is unavailable (private mode, etc.)
    console.error('Could not save newsletter subscription:', error);
    return false;
  }
}

// ------------------------------------------------------------
// 7. SHARED MODAL HELPERS
//    One reusable modal container is created once and then
//    filled with HTML by the page-specific scripts.
// ------------------------------------------------------------
function ensureModalContainer() {
  // Already built? Return early.
  if (document.getElementById('shared-modal')) return;

  // Build the modal markup with template literals
  const modal = document.createElement('div');
  modal.className = 'modal';
  modal.id = 'shared-modal';

  modal.innerHTML = `
    <div class="modal__box">
      <button class="modal__close" aria-label="Close">&times;</button>
      <div class="modal__content"></div>
    </div>
  `;

  document.body.appendChild(modal);

  const closeButton = modal.querySelector('.modal__close');

  // Close button click
  closeButton.addEventListener('click', window.closeModal);

  // Click on the dark backdrop (but not the box) closes the modal
  modal.addEventListener('click', function (event) {
    if (event.target === modal) {
      window.closeModal();
    }
  });

  // Escape key closes the modal
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape' && modal.classList.contains('open')) {
      window.closeModal();
    }
  });
}

// Opens the shared modal and fills it with HTML
window.openModal = function (html) {
  ensureModalContainer();

  const modal = document.getElementById('shared-modal');
  const content = modal.querySelector('.modal__content');

  content.innerHTML = html;
  modal.classList.add('open');
  modal.querySelector('.modal__close').focus();

  // Prevent background scrolling while the modal is open
  document.body.style.overflow = 'hidden';
};

// Closes the shared modal
window.closeModal = function () {
  const modal = document.getElementById('shared-modal');
  if (!modal) return;

  modal.classList.remove('open');
  document.body.style.overflow = '';
};

// Escapes HTML characters so text is shown as plain text, not markup
window.escapeHtml = function (value) {
  const htmlEscapeMap = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
  };

  return String(value).replace(/[&<>"']/g, function (character) {
    return htmlEscapeMap[character];
  });
};