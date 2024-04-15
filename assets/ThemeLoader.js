import * as themeManager from 'App/modules/Theme';


export function load() {
    const theme = themeManager.getTheme();
    /** @type {HTMLButtonElement} themeLight */
    const themeLightIconTag = document.querySelector('[data-js-theme-light]');
    /** @type {HTMLButtonElement} themeDarkIconTag */
    const themeDarkIconTag = document.querySelector('[data-js-theme-dark]');
    /** @type {HTMLButtonElement} themeDark */
    const themeAutoIconTag = document.querySelector('[data-js-theme-auto]');

    themeManager.setTheme(theme, themeAutoIconTag, themeLightIconTag, themeDarkIconTag);
}