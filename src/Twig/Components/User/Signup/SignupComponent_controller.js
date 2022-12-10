import { Controller } from '@hotwired/stimulus';
import * as form from '../../../../../assets/modules/form';

/*
 * This is an example Stimulus controller!
 *
 * Any element with a data-controller="hello" attribute will cause
 * this controller to be executed. The name "hello" comes from the filename:
 * hello_controller.js -> "hello"
 *
 * Delete this file or adapt it for your use!
 */
export default class extends Controller {
    connect() {
        this.formValidate();
    }

    formValidate() {
        form.validate(this.element, this.validatePasswordRepeat.bind());
        this.element.addEventListener('input', this.validatePasswordRepeat);
    }

    validatePasswordRepeat() {
        let password = document.getElementById('password');
        let passwordRepeated = document.getElementById('password_repeated');

        if (password.value == passwordRepeated.value) {
            passwordRepeated.setCustomValidity('');

            return true;
        }
        else {
            passwordRepeated.setCustomValidity('not valid');

            return false;
        }
    }


}
