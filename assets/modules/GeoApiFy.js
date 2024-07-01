
const API_URL = 'https://api.geoapify.com/v1/geocode/autocomplete'
    + '?text={LOCATION}'
    + '&lang={LANGUAGE}'
    + '&limit={RESULT_LIMIT}'
    + '&filter=countrycode:none'
    + '&bias=countrycode:auto'
    + '&format=json'
    + '&apiKey={API_KEY}';

const API_KEY = '02a1340a8f42450fa321d1d11a80be21';
const RESPONSE_ITEMS_NUM = 20;

/**
 * @param {string} location
 * @param {string} lang
 * @param {number} resultLimit
 *
 * @returns {Promise<Response>}
 */
async function request(location, lang, resultLimit) {
    const URL = API_URL
        .replace('{LOCATION}', location)
        .replace('{LANGUAGE}', lang)
        .replace('{RESULT_LIMIT}', resultLimit.toString())
        .replace('{API_KEY}', API_KEY)

    return await fetch(URL);
}

/**
 * @param {string} location
 * @param {string} lang
 *
 * @returns {Promise<string[]>}
 *
 * @throws Error
 */
export async function getAddresses(location, lang) {
    const responseData = await request(location, lang, RESPONSE_ITEMS_NUM);

    if (!responseData.ok) {
        throw new Error(`HTTP error: ${responseData.status}`);
    }

    const responseJson = await responseData.json();

    return responseJson['results'].map((addressData) => addressData.formatted);
}
