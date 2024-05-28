import * as themeManager from 'App/modules/Theme';


export function load() {
    const theme = themeManager.getTheme();
    /** @type {HTMLButtonElement} themeLightIconTag */
    const themeLightIconTag = document.querySelector('[data-js-theme-light]');
    /** @type {HTMLButtonElement} themeDarkIconTag */
    const themeDarkIconTag = document.querySelector('[data-js-theme-dark]');
    /** @type {HTMLButtonElement} themeAutoIconTag */
    const themeAutoIconTag = document.querySelector('[data-js-theme-auto]');

    themeManager.setTheme(theme, themeAutoIconTag, themeLightIconTag, themeDarkIconTag);
}