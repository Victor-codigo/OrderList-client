import { Controller } from '@hotwired/stimulus';
import * as form from 'App/modules/form';


export default class extends Controller {
    /**
     * @type {HTMLInputElement}
     */
    #dateToBuyTag;

    connect() {
        this.#dateToBuyTag = this.element.querySelector('[data-js-list-orders-date-to-buy]');
        this.#dateToBuyTag.min = new Date().toISOString().slice(0, -8);

        this.formValidate();
    }

    formValidate() {
        form.validate(this.element, null);
    }
}

