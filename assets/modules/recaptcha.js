const SITE_KEY = process.env.RECAPTCHA3_KEY;
console.log('SITE_KEY' + SITE_KEY);

loadReCaptcha();


/**
 * Search a form with an attribute "data-recaptcha", if exists. adds recaptcha
 */
function loadReCaptcha() {
    /** @type {HTMLFormElement} */
    const formTag = document.querySelector('form[data-recaptcha]');

    if (formTag === null) {
        return;
    }

    const inputReCaptchaTag = createInputReCaptcha(formTag, formTag.dataset.recaptcha);

    loadRemoteScript(
        'https://www.google.com/recaptcha/api.js'
        + '?render=' + SITE_KEY,
        () => this.grecaptcha.ready(async () => inputReCaptchaTag.value = await getReCaptchaV3Token())
    );
}

/**
 * @param {HTMLFormElement} formTag
 * @param {string} nameAttribute
 *
 * @returns {HTMLInputElement}
 */
function createInputReCaptcha(formTag, nameAttribute) {
    const captchaInput = document.createElement('input');
    captchaInput.type = 'hidden';
    captchaInput.name = nameAttribute;

    formTag.appendChild(captchaInput);

    return captchaInput;
}

/**
 * @returns {Promise<string>}
 *
 * @throws {error}
 */
async function getReCaptchaV3Token() {

    try {
        const token = await this.grecaptcha.execute(
            SITE_KEY, {
            action: 'landing'
        })

        return token;
    } catch (error) {
        throw error;
    }
};

/**
 * @param {string} url
 * @param {*} onLoadCallback
 */
function loadRemoteScript(url, onLoadCallback) {
    var scriptRemote = document.createElement('script');
    scriptRemote.src = url;
    scriptRemote.async = true;
    scriptRemote.defer = true;
    scriptRemote.onload = onLoadCallback;
    document.head.appendChild(scriptRemote);
}
