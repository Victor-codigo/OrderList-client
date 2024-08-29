import * as themeManager from 'App/modules/Theme';

export function updateThemeButtons() {
    const theme = themeManager.getTheme();
    const navigationBar = document.querySelector('[data-js-navigation-bar]')
    /** @type {HTMLButtonElement[]} themeLight */
    const themeLightIconTags = navigationBar.querySelectorAll('[data-js-theme-light]');
    /** @type {HTMLButtonElement[]} themeDarkIconTag */
    const themeDarkIconTags = navigationBar.querySelectorAll('[data-js-theme-dark]');
    /** @type {HTMLButtonElement[]} themeDark */
    const themeAutoIconTags = navigationBar.querySelectorAll('[data-js-theme-auto]');

    for (let index in Array.from(themeAutoIconTags)) {
        themeManager.setTheme(theme, themeAutoIconTags[index], themeLightIconTags[index], themeDarkIconTags[index]);
    }
}

export function setTheme() {
    const theme = themeManager.getTheme();
    themeManager.setTheme(theme, null, null, null)
}