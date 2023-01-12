import { Controller } from '@hotwired/stimulus';
import * as form from '../../../../../assets/modules/form';


export default class extends Controller {
    connect() {
        this.formValidate();
    }

    formValidate() {
        form.validate(this.element);
        this.element.addEventListener('input', callback => form.validatePasswordRepeat(
            document.getElementById('password_new'),
            document.getElementById('password_new_repeat')
        ));
    }
}