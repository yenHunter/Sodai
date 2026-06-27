// resources/js/pages/admin-auth-reset-password.js

/**
 * Admin Reset Password Page
 * Handles: reCAPTCHA v3, password toggle, strength indicator
 */

document.addEventListener('DOMContentLoaded', function () {
    initPasswordToggle();
    initPasswordStrength();
    initResetPasswordForm();
});

// ─────────────────────────────────────────────
// PASSWORD TOGGLE
// ─────────────────────────────────────────────

function initPasswordToggle() {
    const toggleBtn = document.getElementById('togglePassword');

    if (!toggleBtn) return;

    toggleBtn.addEventListener('click', function () {
        const input  = document.getElementById('password');
        const icon   = document.getElementById('toggleIcon');
        const isHide = input.type === 'password';

        input.type = isHide ? 'text' : 'password';
        icon.classList.toggle('bi-eye',      !isHide);
        icon.classList.toggle('bi-eye-slash', isHide);
    });
}

// ─────────────────────────────────────────────
// PASSWORD STRENGTH
// ─────────────────────────────────────────────

function initPasswordStrength() {
    const passwordInput = document.getElementById('password');

    if (!passwordInput) return;

    passwordInput.addEventListener('input', function () {
        const result = checkStrength(this.value);
        updateStrengthUI(result);
    });
}

function checkStrength(password) {
    let strength = 0;

    if (password.length >= 8)          strength++;
    if (password.length >= 12)         strength++;
    if (/[A-Z]/.test(password))        strength++;
    if (/[0-9]/.test(password))        strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    const levels = [
        { label: '',            color: 'bg-secondary', width: 0   },
        { label: 'Very Weak',   color: 'bg-danger',    width: 20  },
        { label: 'Weak',        color: 'bg-danger',    width: 40  },
        { label: 'Fair',        color: 'bg-warning',   width: 60  },
        { label: 'Strong',      color: 'bg-info',      width: 80  },
        { label: 'Very Strong', color: 'bg-success',   width: 100 },
    ];

    return {
        ...levels[strength],
        empty: password.length === 0,
    };
}

function updateStrengthUI({ label, color, width, empty }) {
    const bar  = document.getElementById('strengthBar');
    const text = document.getElementById('strengthText');

    if (!bar || !text) return;

    bar.style.width  = empty ? '0%' : `${width}%`;
    bar.className    = `progress-bar ${color}`;
    text.textContent = empty ? '' : label;
    text.className   = `small ${getTextColor(color)}`;
}

function getTextColor(bgColor) {
    const map = {
        'bg-danger'  : 'text-danger',
        'bg-warning' : 'text-warning',
        'bg-info'    : 'text-info',
        'bg-success' : 'text-success',
        'bg-secondary': 'text-muted',
    };
    return map[bgColor] ?? 'text-muted';
}

// ─────────────────────────────────────────────
// RESET PASSWORD FORM WITH RECAPTCHA V3
// ─────────────────────────────────────────────

function initResetPasswordForm() {
    const form      = document.getElementById('resetPasswordForm');
    const submitBtn = document.getElementById('resetBtn');
    const siteKey   = document.getElementById('recaptchaSiteKey')?.value;

    if (!form || !siteKey) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

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
        : '<i class="bi bi-check-circle me-2"></i>Reset Password';
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