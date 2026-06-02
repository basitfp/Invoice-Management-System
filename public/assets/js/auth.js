/**
 * Custom Authentication Validation Script
 * Implements real-time validation via jQuery on form inputs
 * Handles Show/Hide Password and prevents duplicate form submissions
 */

$(document).ready(function () {
    const $form = $('#login-form');
    const $emailInput = $('#email');
    const $passwordInput = $('#password');
    const $submitBtn = $('#btn-submit');
    const $passwordToggle = $('#password-toggle');
    const $emailFeedback = $('#email-feedback');
    const $passwordFeedback = $('#password-feedback');

    // Email regex validation pattern
    function isValidEmail(email) {
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return emailRegex.test(email);
    }

    // Toggle Password Visibility (without page reload)
    $passwordToggle.on('click', function () {
        const inputType = $passwordInput.attr('type');
        const $icon = $(this).find('i');
        
        if (inputType === 'password') {
            $passwordInput.attr('type', 'text');
            $icon.removeClass('bi-eye').addClass('bi-eye-slash');
        } else {
            $passwordInput.attr('type', 'password');
            $icon.removeClass('bi-eye-slash').addClass('bi-eye');
        }
    });

    // Prevent spacebar character in the email field
    $emailInput.on('keydown', function (e) {
        if (e.which === 32) {
            e.preventDefault();
        }
    });

    // Real-time Email Field Checks
    function validateEmail() {
        let value = $emailInput.val();
        
        // Strip out spaces
        if (/\s/g.test(value)) {
            value = value.replace(/\s/g, '');
            $emailInput.val(value);
        }

        if (value.length === 0) {
            $emailInput.addClass('is-invalid');
            $emailFeedback.text('The email field is required.');
            return false;
        } else if (!isValidEmail(value)) {
            $emailInput.addClass('is-invalid');
            $emailFeedback.text('Please enter a valid email address.');
            return false;
        } else {
            $emailInput.removeClass('is-invalid');
            $emailFeedback.text('');
            return true;
        }
    }

    // Real-time Password Field Checks
    function validatePassword() {
        let value = $passwordInput.val();

        // Strip spaces from password
        if (/\s/g.test(value)) {
            value = value.replace(/\s/g, '');
            $passwordInput.val(value);
        }

        if (value.length === 0) {
            $passwordInput.addClass('is-invalid');
            $passwordFeedback.text('The password field is required.');
            return false;
        } else {
            $passwordInput.removeClass('is-invalid');
            $passwordFeedback.text('');
            return true;
        }
    }

    // Check Form Validity and update button state
    function checkFormValidity() {
        const emailValue = $emailInput.val().trim();
        const passwordValue = $passwordInput.val();

        const isEmailValid = emailValue.length > 0 && isValidEmail(emailValue);
        const isPasswordValid = passwordValue.length > 0;

        if (isEmailValid && isPasswordValid) {
            $submitBtn.prop('disabled', false);
        } else {
            $submitBtn.prop('disabled', true);
        }
    }

    // Event binding: run checks on keyup, keydown, input, change, blur, and paste
    $emailInput.on('keyup keydown input change blur paste', function () {
        setTimeout(function() {
            validateEmail();
            checkFormValidity();
        }, 10);
    });

    $passwordInput.on('keyup keydown input change blur paste', function () {
        setTimeout(function() {
            validatePassword();
            checkFormValidity();
        }, 10);
    });

    // Run check on load (helpful if user has autofill details cached)
    setTimeout(function() {
        if ($emailInput.val().length > 0) {
            validateEmail();
        }
        if ($passwordInput.val().length > 0) {
            validatePassword();
        }
        checkFormValidity();
    }, 100);

    // Prevent double form submission on click
    $form.on('submit', function (e) {
        if (!validateEmail() || !validatePassword()) {
            e.preventDefault();
            return false;
        }
        // Disable button, change text, prevent secondary submit triggers
        $submitBtn.prop('disabled', true);
        $submitBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Logging In...');
    });
});
