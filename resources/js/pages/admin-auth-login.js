/**
 * Admin Login Page
 * Handles: reCAPTCHA v3, password toggle, back button prevention
 */

document.addEventListener('DOMContentLoaded', function () {
    initLoginForm();
    preventBackButton();
});

// ─────────────────────────────────────────────
// LOGIN FORM WITH RECAPTCHA V3
// ─────────────────────────────────────────────

function initLoginForm() {
    const form      = document.getElementById('adminLoginForm');
    const submitBtn = document.getElementById('loginBtn');
    const siteKey   = document.getElementById('recaptchaSiteKey')?.value;

    if (!form || !siteKey) {
        console.error('reCAPTCHA: Form or site key not found');
        return;
    }

    console.log('reCAPTCHA: Form initialized with site key:', siteKey);

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // Disable button - prevent double submit
        setLoadingState(submitBtn, true);

        // Execute reCAPTCHA v3
        grecaptcha.ready(function () {
            console.log('reCAPTCHA: grecaptcha ready, executing...');
            grecaptcha.execute(siteKey, { action: 'admin_login' })
                .then(function (token) {
                    console.log('reCAPTCHA: Token received, length:', token.length);
                    // Inject token into hidden field
                    document.getElementById('g-recaptcha-response').value = token;
                    console.log('reCAPTCHA: Token injected into form');

                    // Submit form
                    form.submit();
                })
                .catch(function (error) {
                    console.error('reCAPTCHA: Error executing', error);
                    setLoadingState(submitBtn, false);
                    showAlert('reCAPTCHA failed to load. Please refresh the page.');
                });
        });
    });
}

// ─────────────────────────────────────────────
// PREVENT BACK BUTTON
// ─────────────────────────────────────────────

function preventBackButton() {
    window.history.pushState(null, null, window.location.href);
    window.addEventListener('popstate', function () {
        window.history.pushState(null, null, window.location.href);
    });
}

// ─────────────────────────────────────────────
// HELPERS
// ─────────────────────────────────────────────

function setLoadingState(button, isLoading) {
    if (!button) return;

    button.disabled = isLoading;
    button.innerHTML = isLoading
        ? 'Verifying...'
        : 'Sign In';
}

function showAlert(message) {
    alert(message);
}