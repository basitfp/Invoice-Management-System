document.addEventListener('DOMContentLoaded', function () {

    var FV = window.FormValidation;

    function getSettingsRules() {
        return [
            {
                field: $('#app_name'),
                label: 'App name',
                required: true,
                requiredMessage: 'App name is required.'
            },
            {
                field: $('#settings_address'),
                label: 'Address',
                required: true,
                requiredMessage: 'Address is required.'
            },
            FV.rules.phone($('#settings_phone'), 'Phone number'),
            FV.rules.email($('#settings_email'), 'Email'),
            {
                field: $('#settings_bank_name'),
                label: 'Bank name',
                required: true,
                requiredMessage: 'Bank name is required.'
            },
            {
                field: $('#settings_iban'),
                label: 'IBAN',
                required: true,
                requiredMessage: 'IBAN is required.'
            },
            {
                field: $('#settings_swift_code'),
                label: 'SWIFT code',
                required: true,
                requiredMessage: 'SWIFT code is required.'
            }
        ];
    }

    function isSettingsFormValid() {
        if (!FV) {
            return true;
        }
        return FV.checkRules(getSettingsRules());
    }

    function updateSettingsSaveButton() {
        var btn = document.getElementById('settingsSaveBtn');
        if (!btn) {
            return;
        }
        btn.disabled = !isSettingsFormValid();
    }

    function validateSettingsForm() {
        if (!FV) {
            return true;
        }

        var valid = FV.runRules(getSettingsRules(), true);

        if (!valid && typeof window.showGlobalValidationError === 'function') {
            window.showGlobalValidationError();
        }

        updateSettingsSaveButton();
        return valid;
    }

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
        var settingsFields = settingsForm.querySelectorAll(
            '#app_name, #settings_address, #settings_email, #settings_phone, #settings_bank_name, #settings_iban, #settings_swift_code'
        );

        settingsFields.forEach(function (field) {
            field.addEventListener('input', updateSettingsSaveButton);
            field.addEventListener('blur', updateSettingsSaveButton);
            field.addEventListener('change', updateSettingsSaveButton);
        });

        updateSettingsSaveButton();

        settingsForm.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!validateSettingsForm()) {
                return;
            }

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
                    if (xhr.status === 422 && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        var idMap = {
                            app_name: '#app_name',
                            email: '#settings_email',
                            phone: '#settings_phone',
                            address: '#settings_address',
                            bank_name: '#settings_bank_name',
                            iban: '#settings_iban',
                            swift_code: '#settings_swift_code'
                        };
                        Object.keys(errors).forEach(function (key) {
                            if (idMap[key]) {
                                FV.setFieldError($(idMap[key]), errors[key][0]);
                            }
                        });
                        if (typeof window.showGlobalValidationError === 'function') {
                            window.showGlobalValidationError();
                        }
                    } else {
                        if (typeof toastr !== 'undefined') toastr.error('Something went wrong while saving settings.');
                    }
                }
            });
        });
    }

});