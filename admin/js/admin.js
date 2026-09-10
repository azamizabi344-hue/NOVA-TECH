/**
 * NOVA TECH - Admin JavaScript
 * Handles:
 * - Mobile sidebar toggle
 * - Fetch delete actions (uses fetch + confirm)
 * - CSRF token helper for AJAX calls
 */

document.addEventListener('DOMContentLoaded', function () {
  // ============================================================
  // MOBILE SIDEBAR TOGGLE
  // ============================================================
  const menuToggle = document.getElementById('adminMenuToggle');
  const sidebar = document.getElementById('adminSidebar');

  if (menuToggle && sidebar) {
    menuToggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function (e) {
      if (window.innerWidth <= 1024 &&
          !sidebar.contains(e.target) &&
          !menuToggle.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  }

  // ============================================================
  // CSRF TOKEN - read from the hidden token cookie/meta
  // The token is injected into the DOM on each page via csrf_field()
  // ============================================================
  window.getCsrfToken = function () {
    // Try to find a form with a csrf token to reuse it
    const input = document.querySelector('input[name="csrf_token"]');
    if (input) return input.value;
    return '';
  }
});