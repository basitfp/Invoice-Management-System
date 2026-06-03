document.addEventListener('DOMContentLoaded', function () {

    // =============================================
    // LIVE PREVIEW - Update as user types
    // =============================================
    function liveSync(inputId, previewId, fallback) {
        var input   = document.getElementById(inputId);
        var preview = document.getElementById(previewId);
        if (!input || !preview) return;

        input.addEventListener('input', function () {
            preview.innerText = this.value.trim() || fallback;
        });
    }

    liveSync('app_name', 'sip-name',    'Company Name');

    // Address, phone, email use name attributes — query by name
    var addressInput = document.querySelector('[name="address"]');
    var phoneInput   = document.querySelector('[name="phone"]');
    var emailInput   = document.querySelector('[name="email"]');
    var bankInput    = document.querySelector('[name="bank_name"]');
    var ibanInput    = document.querySelector('[name="iban"]');
    var swiftInput   = document.querySelector('[name="swift_code"]');

    if (addressInput) {
        addressInput.addEventListener('input', function () {
            document.getElementById('sip-address').innerText = this.value.trim() || 'Business Address';
        });
    }
    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            document.getElementById('sip-phone').innerText = this.value.trim() || '+44 0000 000000';
        });
    }
    if (emailInput) {
        emailInput.addEventListener('input', function () {
            document.getElementById('sip-email').innerText = this.value.trim() || 'email@company.com';
        });
    }
    if (bankInput) {
        bankInput.addEventListener('input', function () {
            document.getElementById('sip-bank').innerText = this.value.trim() || 'Bank Name';
        });
    }
    if (ibanInput) {
        ibanInput.addEventListener('input', function () {
            document.getElementById('sip-iban').innerText = this.value.trim() || 'IBAN';
        });
    }
    if (swiftInput) {
        swiftInput.addEventListener('input', function () {
            document.getElementById('sip-swift').innerText = this.value.trim() || 'SWIFT';
        });
    }


    // =============================================
    // LOGO UPLOAD - Preview in dropzone + live preview
    // =============================================
    var fileInput      = document.getElementById('logo-file');
    var dropzone       = document.getElementById('logo-dropzone');
    var previewWrap    = document.getElementById('logo-preview-wrap');
    var previewImg     = document.getElementById('logo-preview-img');
    var removeBtn      = document.getElementById('removeLogo');
    var removeFlag     = document.getElementById('remove_logo_flag');
    var sipLogo        = document.getElementById('sip-logo');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    // Show preview box
                    previewImg.src = e.target.result;
                    previewWrap.classList.remove('d-none');
                    dropzone.classList.add('d-none');

                    // Update live preview
                    sipLogo.src = e.target.result;
                    sipLogo.style.display = 'block';

                    // Reset remove flag
                    removeFlag.value = '0';
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    }

    // Drag & drop on dropzone
    if (dropzone) {
        dropzone.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.style.borderColor = 'var(--primary-color)';
        });

        dropzone.addEventListener('dragleave', function () {
            this.style.borderColor = '';
        });

        dropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            this.style.borderColor = '';
            var files = e.dataTransfer.files;
            if (files && files[0]) {
                fileInput.files = files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    }

    // Remove logo
    if (removeBtn) {
        removeBtn.addEventListener('click', function () {
            previewImg.src = '';
            previewWrap.classList.add('d-none');
            dropzone.classList.remove('d-none');
            fileInput.value = '';
            removeFlag.value = '1';

            sipLogo.src = '';
            sipLogo.style.display = 'none';
        });
    }

    // =============================================
    // AJAX FORM SUBMISSION
    // =============================================
    var settingsForm = document.getElementById('settingsForm');
    if (settingsForm) {
        settingsForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Stop normal reload

            // Remove old invalid classes
            var invalidInputs = settingsForm.querySelectorAll('.is-invalid');
            invalidInputs.forEach(function(input) { input.classList.remove('is-invalid'); });
            var oldFeedbacks = settingsForm.querySelectorAll('.invalid-feedback.ajax-feedback, .text-danger.small.mt-1.ajax-feedback');
            oldFeedbacks.forEach(function(el) { el.remove(); });

            var formData = new FormData(this);
            var formAction = this.action;

            $.ajax({
                url: formAction,
                type: 'POST', // Method overriding is handled by _method=PUT in formData
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                success: function (response) {
                    if (response.success) {
                        if (typeof toastr !== 'undefined') {
                            toastr.success(response.message);
                        }
                        // Reload the page to ensure the new logo and settings are fully applied everywhere
                        setTimeout(function() {
                            window.location.reload();
                        }, 1000);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 422) {
                        var errors = xhr.responseJSON.errors;
                        for (var field in errors) {
                            var input = settingsForm.querySelector('[name="' + field + '"]');
                            if (input) {
                                input.classList.add('is-invalid');
                                var errorMsg = '<div class="invalid-feedback ajax-feedback d-block">' + errors[field][0] + '</div>';
                                input.insertAdjacentHTML('afterend', errorMsg);
                            }
                        }
                        if (typeof toastr !== 'undefined') toastr.error('Please fix the validation errors.');
                    } else {
                        if (typeof toastr !== 'undefined') toastr.error('Something went wrong while saving settings.');
                    }
                }
            });
        });
    }

});