/* ==========================================================================
   auth.js
   Client-side demo authentication for the NOVA TECH site.

   Everything is stored in localStorage only — nothing is sent to a server.

   Features:
   - Predefined demo user accounts
   - Login form validation + credential check
   - "Remember me" (saves the email for next time)
   - Show/hide password toggle
   - Session stored in localStorage
   - Logout (clears the session)
   - Navbar Login button switches to a "Dashboard" link while logged in

   Exposes a small API on `window.auth`:
     - window.auth.getUser()   -> session object or null
     - window.auth.isLoggedIn() -> true/false
     - window.auth.logout()     -> clears session (+ redirects if on dashboard)

   Demonstrates: const, let, arrays, objects, functions, parameters,
   return, if/else, for...of, array methods (find, trim, toLowerCase),
   template literals, DOM manipulation, form + change events, classList,
   localStorage + JSON, Date, try/catch.
   ========================================================================== */

// --------------------------------------------------------------------------
// 1. DEMO USER DATABASE (hardcoded for the demo)
// --------------------------------------------------------------------------
const demoUsers = [
  {
    name: 'Admin',
    email: 'admin@novatech.com',
    password: 'admin123',
    role: 'Administrator',
  },
  {
    name: 'Jane Cooper',
    email: 'jane@novatech.com',
    password: 'jane123',
    role: 'Project Manager',
  },
];

// Where we store session data in localStorage
const SESSION_KEY = 'novatech_session';
const REMEMBER_KEY = 'novatech_remembered_email';

// --------------------------------------------------------------------------
// 2. SESSION HELPERS (exposed publicly through window.auth)
// --------------------------------------------------------------------------
// Reads the current session, or returns null if absent / corrupted
function getCurrentUser() {
  try {
    const raw = localStorage.getItem(SESSION_KEY);
    if (!raw) return null;

    const session = JSON.parse(raw);
    return session.user ? session.user : null;
  } catch (error) {
    console.error('Could not read the session:', error);
    return null;
  }
}

function isLoggedIn() {
  return getCurrentUser() !== null;
}

// Clears the session. On the dashboard, redirect back to the login page.
function logout(shouldRedirect) {
  localStorage.removeItem(SESSION_KEY);

  if (shouldRedirect) {
    window.location.href = 'login.html';
  }
}

// Public API used by other scripts (like dashboard.js and the navbar)
window.auth = {
  getUser: getCurrentUser,
  isLoggedIn: isLoggedIn,
  logout: function () {
    logout(true);
  },
};

// --------------------------------------------------------------------------
// 3. NAVBAR "LOGIN" BUTTON
//    If a user is already logged in, turn the button into a Dashboard link.
//    Runs on every page that includes the shared navbar.
// --------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
  updateNavbarButton();
});

function updateNavbarButton() {
  const loginButton = document.getElementById('navbar-login');
  if (!loginButton) return;

  const user = getCurrentUser();

  if (user) {
    loginButton.textContent = 'Dashboard';
    loginButton.href = 'dashboard.html';
    loginButton.classList.add('btn--primary');
    loginButton.classList.remove('btn--ghost');
  } else {
    loginButton.textContent = 'Login';
    loginButton.href = 'login.html';
    loginButton.classList.remove('btn--primary');
    loginButton.classList.add('btn--ghost');
  }
}

// --------------------------------------------------------------------------
// 4. LOGIN FORM (only exists on login.html)
// --------------------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
  initLoginForm();
});

function initLoginForm() {
  const form = document.getElementById('login-form');
  if (!form) return; // guard: only render when the form exists

  const emailInput = document.getElementById('login-email');
  const passwordInput = document.getElementById('login-password');
  const rememberCheck = document.getElementById('remember-me') || { checked: false };
  const showPasswordCheck = document.getElementById('show-password');
  const alertBox = document.getElementById('auth-alert');

  // a) Prefill the email if "remember me" was used before
  const rememberedEmail = localStorage.getItem(REMEMBER_KEY);
  if (rememberedEmail) {
    emailInput.value = rememberedEmail;
    rememberCheck.checked = true;
  }

  // b) Show / hide password toggle (change event)
  showPasswordCheck.addEventListener('change', function () {
    if (showPasswordCheck.checked) {
      passwordInput.type = 'text'; // reveal the password
    } else {
      passwordInput.type = 'password'; // hide it again
    }
  });

  // c) Submit: validate fields and check credentials
  form.addEventListener('submit', function (event) {
    event.preventDefault();

    const email = emailInput.value.trim();
    const password = passwordInput.value;

    // Validate both fields are filled
    if (email === '' || password === '') {
      showAuthError('Please enter both email and password.');
      if (email === '') setLoginError(emailInput, 'Email is required.');
      else clearLoginError(emailInput);
      if (password === '') setLoginError(passwordInput, 'Password is required.');
      else clearLoginError(passwordInput);
      return;
    }

    // Find a matching demo user (case-insensitive email)
    const user = demoUsers.find(function (u) {
      return u.email.toLowerCase() === email.toLowerCase() && u.password === password;
    });

    if (!user) {
      // else-if chain alternative: no user matched
      showAuthError('Invalid email or password. Please try again.');
      return;
    }

    // Success: hide any old errors and create the session
    hideAuthError();
    clearLoginError(emailInput);
    clearLoginError(passwordInput);

    const session = {
      user: {
        name: user.name,
        email: user.email,
        role: user.role,
      },
      loginAt: new Date().toISOString(), // Date object + ISO string
    };

    // Persist the session (wrapped in try/catch)
    try {
      localStorage.setItem(SESSION_KEY, JSON.stringify(session));
    } catch (error) {
      console.error('Could not save the session:', error);
    }

    // Remember the email if the checkbox is checked, otherwise forget it
    if (rememberCheck.checked) {
      localStorage.setItem(REMEMBER_KEY, email);
    } else {
      localStorage.removeItem(REMEMBER_KEY);
    }

    // Redirect to the dashboard
    window.location.href = 'dashboard.html';
  });
}

// --------------------------------------------------------------------------
// 5. SMALL UI HELPERS
// --------------------------------------------------------------------------
function showAuthError(message) {
  const alertBox = document.getElementById('auth-alert');
  alertBox.textContent = message;
  alertBox.classList.add('show');
}

function hideAuthError() {
  const alertBox = document.getElementById('auth-alert');
  alertBox.classList.remove('show');
}

function setLoginError(input, message) {
  const group = input.closest('.form__group');
  if (group) {
    group.classList.add('error');
    const errorElement = group.querySelector('.form__error');
    errorElement.textContent = message;
  }
}

function clearLoginError(input) {
  const group = input.closest('.form__group');
  if (group) {
    group.classList.remove('error');
    const errorElement = group.querySelector('.form__error');
    errorElement.textContent = '';
  }
}