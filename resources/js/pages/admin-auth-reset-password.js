/**
 * Admin Reset Password Page
 * Handles: reCAPTCHA v3, password toggle, strength indicator
 */

document.addEventListener('DOMContentLoaded', function () {
    initResetPasswordForm();
});

// ─────────────────────────────────────────────
// RESET PASSWORD FORM WITH RECAPTCHA V3
// ─────────────────────────────────────────────

function initResetPasswordForm() {
    const form      = document.getElementById('resetPasswordForm');
    const submitBtn = document.getElementById('resetBtn');
    const siteKey   = document.getElementById('recaptchaSiteKey')?.value;
    const checkbox  = document.getElementById('termAndPolicy');

    if (!form || !siteKey) return;

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

        grecaptcha.ready(function () {
            grecaptcha.execute(siteKey, { action: 'admin_reset_password' })
                .then(function (token) {
                    document.getElementById('g-recaptcha-response').value = token;
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
        ? '<i class="bi bi-hourglass-split me-2"></i>Resetting...'
        : '<i class="bi bi-check-circle me-2"></i>Update Password';
}

function showError(message) {
    const errorContainer = document.getElementById('jsErrorContainer');

    if (errorContainer) {
        errorContainer.textContent = message;
        errorContainer.classList.remove('d-none');
        return;
    }

    alert(message);
}