/**
 * Admin Forgot Password Page
 * Handles: reCAPTCHA v3, form submission loading state
 */

document.addEventListener('DOMContentLoaded', function () {
    initForgotPasswordForm();
});

// ─────────────────────────────────────────────
// FORGOT PASSWORD FORM WITH RECAPTCHA V3
// ─────────────────────────────────────────────

function initForgotPasswordForm() {
    const form = document.getElementById('forgotPasswordForm');
    const submitBtn = document.getElementById('sendLinkBtn');
    const siteKey = document.getElementById('recaptchaSiteKey')?.value;
    const checkbox = document.getElementById('termAndPolicy');

    if (!form) return;

    // Initialize submit button disabled state according to checkbox
    if (submitBtn && checkbox) {
        submitBtn.disabled = !checkbox.checked;
        checkbox.addEventListener('change', function () {
            submitBtn.disabled = !checkbox.checked;
        });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (checkbox && !checkbox.checked) {
            showError('Please agree to the Terms & Policy before continuing.');
            return;
        }

        setLoadingState(submitBtn, true);

        if (!siteKey || !window.grecaptcha) {
            setLoadingState(submitBtn, false);
            showError('reCAPTCHA not available. Please refresh the page.');
            return;
        }

        grecaptcha.ready(function () {
            grecaptcha.execute(siteKey, { action: 'admin_forgot_password' })
                .then(function (token) {
                    const rcInput = document.getElementById('g-recaptcha-response');
                    if (rcInput) rcInput.value = token;
                    form.submit();
                })
                .catch(function () {
                    setLoadingState(submitBtn, false);
                    showError('reCAPTCHA failed to load. Please refresh the page.');
                });
        });
    });
}

// ─────────────────────────────────────────────
// HELPERS
// ─────────────────────────────────────────────

function setLoadingState(button, isLoading) {
    if (!button) return;

    button.disabled  = isLoading;
    button.innerHTML = isLoading
        ? '<i class="bi bi-hourglass-split me-2"></i>Sending...'
        : '<i class="bi bi-send me-2"></i>Send Reset Link';
}

function showError(message) {
    // Try to show in existing error container
    const errorContainer = document.getElementById('jsErrorContainer');

    if (errorContainer) {
        errorContainer.textContent = message;
        errorContainer.classList.remove('d-none');
        return;
    }

    alert(message);
}