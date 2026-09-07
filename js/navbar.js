/* ==========================================================================
   navbar.js
   - Sticky navbar shadow on scroll
   - Highlights the active page link
   - Mobile hamburger menu toggle
   - Closes the menu on link click and Escape key
   ========================================================================== */

// Wait for the DOM to finish loading before touching elements
document.addEventListener('DOMContentLoaded', function () {
  initNavbar();
});

// Main navbar setup. Split into small functions to keep things readable.
function initNavbar() {
  // Grab the elements we need with querySelector
  const navbar = document.querySelector('#navbar');
  const toggleBtn = document.querySelector('#navbar-toggle');
  const menu = document.querySelector('#navbar-menu');

  // Guard: navbar markup only exists on pages that include the header
  if (!navbar || !toggleBtn || !menu) return;

  // ------------------------------------------------------------
  // 1. Active link highlighting
  //    Compare each nav link's href against the current file name
  //    so the correct menu item is highlighted on every page.
  // ------------------------------------------------------------
  highlightActiveLink(menu);

  // ------------------------------------------------------------
  // 2. Sticky navbar shadow
  //    Adds a "scrolled" class once the page is scrolled down,
  //    and removes it again when back near the top.
  // ------------------------------------------------------------
  window.addEventListener('scroll', function () {
    if (window.scrollY > 10) {
      navbar.classList.add('navbar--scrolled');
    } else {
      navbar.classList.remove('navbar--scrolled');
    }
  });

  // ------------------------------------------------------------
  // 3. Hamburger menu toggle (mobile only)
  //    Clicking the toggle opens/closes the menu and animates
  //    the three bars into an "X".
  // ------------------------------------------------------------
  toggleBtn.addEventListener('click', function () {
    toggleMenu(menu, toggleBtn, true);
  });

  // ------------------------------------------------------------
  // 4. Close the mobile menu automatically
  //    a) when a link inside the menu is clicked
  //    b) when the Escape key is pressed
  // ------------------------------------------------------------

  // Query ALL anchor links within the menu
  const menuLinks = menu.querySelectorAll('a');

  // a) Loop over every link with a classic for...of loop
  for (const link of menuLinks) {
    link.addEventListener('click', function () {
      closeMenu(menu, toggleBtn);
    });
  }

  // b) Keyboard event: Escape closes the mobile menu
  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      closeMenu(menu, toggleBtn);
    }
  });
}

// Highlights the nav link that matches the current file name
function highlightActiveLink(menu) {
  // Pathname looks like "/about.html" -> we only want "about.html"
  const currentPage = window.location.pathname.split('/').pop() || 'index.html';

  const navLinks = menu.querySelectorAll('a');

  // Loop through each link and check its "href" attribute
  for (const link of navLinks) {
    const href = link.getAttribute('href');

    if (href === currentPage) {
      link.classList.add('active');
    }
  }
}

// Opens / closes the mobile menu (toggles state on the elements)
function toggleMenu(menu, toggleBtn, isButton) {
  // classList.toggle returns true if the class is now present
  const isOpen = menu.classList.toggle('open');

  // Keep the button animation and ARIA state in sync
  toggleBtn.classList.toggle('active', isOpen);
  toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

  // Only used by the click handler above, kept for clarity
  if (isButton) {
    console.log('Mobile menu is now ' + (isOpen ? 'open' : 'closed'));
  }
}

// Closes the mobile menu (used by link clicks and Escape key)
function closeMenu(menu, toggleBtn) {
  menu.classList.remove('open');
  toggleBtn.classList.remove('active');
  toggleBtn.setAttribute('aria-expanded', 'false');
}