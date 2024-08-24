import * as themeManager from 'App/modules/Theme';

export function load() {
    const theme = themeManager.getTheme();
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