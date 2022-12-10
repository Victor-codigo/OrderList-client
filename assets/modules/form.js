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