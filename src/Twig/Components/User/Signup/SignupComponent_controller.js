import { Controller } from '@hotwired/stimulus';
import * as form from 'App/modules/form';

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
        form.validate(this.element);
        this.element.addEventListener('input', callback => form.validatePasswordRepeat(
            document.getElementById('password'),
            document.getElementById('password_repeated')
        ));
    }
}
