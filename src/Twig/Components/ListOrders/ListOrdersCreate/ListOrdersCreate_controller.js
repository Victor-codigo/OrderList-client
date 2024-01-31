import { Controller } from '@hotwired/stimulus';
import * as form from 'App/modules/form';


export default class extends Controller {
    connect() {
        this.formValidate();
    }

    formValidate() {
        form.validate(this.element);
    }
}

