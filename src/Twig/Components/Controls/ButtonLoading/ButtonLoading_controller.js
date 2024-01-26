import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.spinnerTag = this.element.querySelector('[data-js-spinner]')
        this.textButtonTag = this.element.querySelector('[data-js-button-text]')
        this.textButtonLoadingTag = this.element.querySelector('[data-js-button-text-loading]')

        this.element.addEventListener('click', this.#showButtonLoading.bind(this));

        this.#showButton();
    }

    disconnect() {
        this.element.removeEventListener('click', this.#showButtonLoading);
    }

    #showButtonLoading() {
        this.element.setAttribute('disabled', true);
        this.textButtonTag.style.display = 'none';

        this.spinnerTag.style.display = 'inline-block';
        this.textButtonLoadingTag.style.display = 'inline-block';
    }

    #showButton() {
        this.element.removeAttribute('disabled');
        this.spinnerTag.style.display = 'none';
        this.textButtonLoadingTag.style.display = 'none';

        this.textButtonTag.style.display = 'inline-block';
    }
}