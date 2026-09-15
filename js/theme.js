// theme.js
// Light / dark theme toggle.
// The html[data-theme] attribute drives all theme colors:
//   - no attribute  -> light theme (default)
//   - data-theme="dark" -> dark theme
// The choice is saved to localStorage so it survives page loads.
// A small inline script in each page <head> applies the saved theme
// before the first paint to avoid any flash of the wrong theme.

(function () {
  'use strict';

  var STORAGE_KEY = 'nova-theme';
  var root = document.documentElement;

  function getTheme() {
    return root.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
  }

  function applyTheme(theme) {
    if (theme === 'dark') {
      root.setAttribute('data-theme', 'dark');
    } else {
      root.removeAttribute('data-theme');
    }
    try {
      localStorage.setItem(STORAGE_KEY, theme);
    } catch (e) {
      // Private browsing / storage unavailable - ignore.
    }
    updateToggle();
  }

  function updateToggle() {
    var toggle = document.getElementById('theme-toggle');
    if (!toggle) return;
    var isDark = getTheme() === 'dark';
    toggle.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
    toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
  }

  function init() {
    var toggle = document.getElementById('theme-toggle');
    if (!toggle) return;

    toggle.addEventListener('click', function () {
      applyTheme(getTheme() === 'dark' ? 'light' : 'dark');
    });

    updateToggle();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();