import { Controller } from '@hotwired/stimulus';

const REDIRECT_TIME = 5000;

export default class extends Controller {

    /**
     * @type {HTMLFormElement}
     */
    #loginForm;

    connect() {
        this.#loginForm = this.element.querySelector('#try-out-login-form');

        this.redirectLogin();
    }

    redirectLogin() {
        setTimeout(
            () => this.#loginForm.submit(),
            REDIRECT_TIME
        );
    }
}
