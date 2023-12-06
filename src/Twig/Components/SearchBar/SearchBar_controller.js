import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {
        this.searchBarFormTag = this.element.querySelector('[data-js-searchbar-form]');
        this.valueTag = this.element.querySelector('[data-js-value]');
        this.filterTag = this.element.querySelector('[data-js-filter]');

        this.searchBarFormTag.addEventListener('submit', this.#onSubmitHandler.bind(this));
    }

    #onSubmitHandler() {
        if (this.valueTag.value == '') {
            this.filterTag.removeAttribute('name');
        }
    }
}
