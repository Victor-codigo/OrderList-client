export function validate(form, validationCallback) {
    'use strict'

    form.addEventListener('submit', event => {
        let formValidation = form.checkValidity();
        let validationCallbackResult = typeof validationCallback === 'function'
            ? validationCallback()
            : true;

        if (!validationCallbackResult || !formValidation) {
            event.preventDefault();
            event.stopPropagation();
        }

        form.classList.add('was-validated');
    }, false);
};

export function validatePasswordRepeat(password, passwordRepeated) {
    'use strict'

    if (password.value === passwordRepeated.value) {
        passwordRepeated.setCustomValidity('');

        return true;
    }

    passwordRepeated.setCustomValidity('not valid');

    return false;
};