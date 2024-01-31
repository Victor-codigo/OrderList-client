/**
 * @callback validationCallback
 */
/**
 * @param {HTMLFormElement} form
 * @param {validationCallback} validationCallback
 */
export function validate(form, validationCallback) {
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

/**
 * @param {HTMLInputElement} password
 * @param {HTMLInputElement} passwordRepeated
 * @returns {boolean}
 */
export function validatePasswordRepeat(password, passwordRepeated) {
    'use strict'

    if (password.value === passwordRepeated.value) {
        passwordRepeated.setCustomValidity('');

        return true;
    }

    passwordRepeated.setCustomValidity('not valid');

    return false;
};