document.addEventListener('DOMContentLoaded', function () {
    initContactForm();
});

function initContactForm() {
    const form = document.getElementById('contactForm');
    const submitBtn = document.getElementById('contactSubmitBtn');
    const siteKey = document.getElementById('recaptchaSiteKey')?.value;

    if (!form) return;

    form.addEventListener('submit', function (e) {
        // Without a configured site key the server still validates the form,
        // so let the browser submit normally instead of blocking the user.
        if (!siteKey || !window.grecaptcha) return;

        e.preventDefault();
        setLoadingState(submitBtn, true);

        grecaptcha.ready(function () {
            grecaptcha.execute(siteKey, { action: 'customer_contact' })
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
