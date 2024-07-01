import { Controller } from '@hotwired/stimulus';
import * as themeManager from 'App/modules/Theme';

export default class extends Controller {
    /**
     * @type {HTMLButtonElement}
     */
    #darkLightModeButton;

    connect() {
        this.#darkLightModeButton = this.element.querySelector('[data-js-theme-button]');
        this.#darkLightModeButton.addEventListener('click', this.#toggleDarkMode.bind(this));
    }

    disconnect() {
        this.#darkLightModeButton.removeEventListener('click', this.#toggleDarkMode);
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
        /** @type {HTMLButtonElement} themeLight */
        const themeLightIconTag = document.querySelector('[data-js-theme-light]');
        /** @type {HTMLButtonElement} themeDarkIconTag */
        const themeDarkIconTag = document.querySelector('[data-js-theme-dark]');
        /** @type {HTMLButtonElement} themeDark */
        const themeAutoIconTag = document.querySelector('[data-js-theme-auto]');

        themeManager.setTheme(theme, themeAutoIconTag, themeLightIconTag, themeDarkIconTag);
    }
}