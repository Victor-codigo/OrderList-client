export const THEMES = {
    DARK: 'dark',
    LIGHT: 'light',
    AUTO: 'auto',
};

/**
 * @returns {string}
 */
export function getTheme() {
    const storageTheme = getStoredTheme();

    if (storageTheme === null) {
        return THEMES.AUTO;
    }

    return storageTheme;
}

/**
 * @returns {string}
 */
export function getThemeDefault() {
    return window.matchMedia('(prefers-color-scheme: dark)').matches ? THEMES.DARK : THEMES.LIGHT;
}

/**
 * @param {string} theme
 */
export function setStoredTheme(theme) {
    localStorage.setItem('theme', theme)
}

/**
 * @returns {string|null}
 */
export function getStoredTheme() {
    return localStorage.getItem('theme');
}

/**
 * @param {string} theme
 * @param {HTMLElement} themeAutoIconTag
 * @param {HTMLElement} themeLightIconTag
 * @param {HTMLElement} themeDarkIconTag
 */
export function setTheme(theme, themeAutoIconTag, themeLightIconTag, themeDarkIconTag) {
    if (theme === THEMES.AUTO) {
        document.documentElement.setAttribute('data-bs-theme', getThemeDefault());

        if (!themeAutoIconTag || !themeLightIconTag || !themeDarkIconTag) {
            return;
        }

        themeLightIconTag.hidden = true;
        themeDarkIconTag.hidden = true;
        themeAutoIconTag.hidden = false;
    } else {
        document.documentElement.setAttribute('data-bs-theme', theme);

        if (!themeAutoIconTag || !themeLightIconTag || !themeDarkIconTag) {
            return;
        }

        if (theme === THEMES.DARK) {
            themeLightIconTag.hidden = false;
            themeAutoIconTag.hidden = true;
            themeDarkIconTag.hidden = true;
        } else {
            themeLightIconTag.hidden = true;
            themeAutoIconTag.hidden = true;
            themeDarkIconTag.hidden = false;
        }
    }
}
