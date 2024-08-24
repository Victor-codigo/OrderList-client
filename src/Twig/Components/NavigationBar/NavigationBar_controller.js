import { Controller } from '@hotwired/stimulus';
import * as themeManager from 'App/modules/Theme';
import * as event from 'App/modules/Event'

export default class extends Controller {

    connect() {
        this.#backButton();

        event.addEventListenerDelegate({
            element: this.element,
            elementDelegateSelector: '[data-js-theme-button]',
            eventName: 'click',
            callbackListener: this.#toggleDarkMode.bind(this),
            eventOptions: {}
        });
    }

    disconnect() {
        event.removeEventListenerDelegate(this.element, 'click', this.#toggleDarkMode);
    }

    #toggleDarkMode() {
        let theme = themeManager.THEMES.LIGHT;

        if (document.documentElement.dataset.bsTheme === themeManager.THEMES.LIGHT) {
            theme = themeManager.THEMES.DARK;
        }

        this.#setTheme(theme);
        themeManager.setStoredTheme(theme);
    }

    /**
     * @param {string} theme
     */
    #setTheme(theme) {
        /** @type {HTMLButtonElement[]} themeLight */
        const themeLightIconTags = document.querySelectorAll('[data-js-theme-light]');
        /** @type {HTMLButtonElement[]} themeDarkIconTag */
        const themeDarkIconTags = document.querySelectorAll('[data-js-theme-dark]');
        /** @type {HTMLButtonElement[]} themeDark */
        const themeAutoIconTags = document.querySelectorAll('[data-js-theme-auto]');

        for (let index in Array.from(themeAutoIconTags)) {
            themeManager.setTheme(theme, themeAutoIconTags[index], themeLightIconTags[index], themeDarkIconTags[index]);
        }
    }

    #backButton() {
        /** @type {HTMLAnchorElement} backButtonTag */
        const backButtonTag = this.element.querySelector('[data-js-back-button]');

        if (backButtonTag === null) {
            sessionStorage.removeItem('backButtonStack');

            return;
        }

        /** @type {string[]} backButtonStack */
        let backButtonStack = JSON.parse(sessionStorage.getItem('backButtonStack') ?? '[]');

        if (backButtonStack.at(-1) === location.href) {
            backButtonStack.pop();
        } else if (!this.#isCurrentPage(backButtonStack)) {
            backButtonStack.push(document.referrer);
        }

        sessionStorage.setItem('backButtonStack', JSON.stringify(backButtonStack));

        backButtonTag.href = backButtonStack.at(-1);
    }

    /**
     * @param {string[]} backButtonStack
     *
     * @returns {boolean}
     */
    #isCurrentPage(backButtonStack) {
        const isAlreadySaved = backButtonStack.some((url) => url === document.referrer);

        if (isAlreadySaved) {
            return true;
        }

        return document.referrer === location.href ? true : false;
    }
}