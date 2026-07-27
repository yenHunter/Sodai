document.addEventListener('DOMContentLoaded', function () {
    initResetPasswordForm();
});

function initResetPasswordForm() {
    const form = document.getElementById('resetPasswordForm');
    const submitBtn = document.getElementById('resetBtn');
    const siteKey = document.getElementById('recaptchaSiteKey')?.value;

    if (!form || !siteKey) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        setLoadingState(submitBtn, true);

        grecaptcha.ready(function () {
            grecaptcha.execute(siteKey, { action: 'customer_reset_password' })
                .then(function (token) {
                    document.getElementById('g-recaptcha-response').value = token;
                    form.submit();
                })
                .catch(function () {
                    setLoadingState(submitBtn, false);
                    alert('reCAPTCHA failed to load. Please refresh the page.');
                });
        });
    });
}

function setLoadingState(button, isLoading) {
    if (!button) return;
    button.disabled = isLoading;
}