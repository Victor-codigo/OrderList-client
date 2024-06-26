import Autocomplete from "App/Dependencies/bootstrap5-autocomplete/autocomplete";


/**
 * @param {Function} requestCallBack
 * @param {string} tagSelector
 * @param {object} config
 */
export function create(requestCallBack, tagSelector, delay = 500, config = {}) {
    const defaultConfig = {
        source: setDelay(async (input, callback) => await callback(await requestCallBack()), delay),
        fullWidth: true,
        fixed: true,
        preventBrowserAutocomplete: true,
    };

    Autocomplete.init(tagSelector, Object.assign(defaultConfig, config));
}

/**
 * @param {Function} callback
 * @param {number} timeout
 * @returns {Function}
 */
function setDelay(callback, timeout = 300) {
    let timer;

    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => callback.apply(this, args), timeout);
    };
}