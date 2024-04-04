import * as cookies from 'App/modules/Cookie.js';

const COOKIE_TOKENSESSION_NAME = 'TOKENSESSION';
const fetchOptionsDefault = {
    'credentials': 'same-origin',
    'mode': 'cors',
    'headers': {
        'Accept': 'application/json',
        'Authorization': `bearer ${cookies.getCookie(COOKIE_TOKENSESSION_NAME)}`
    },
};

/**
 * @param {string} url
 * @param {string} method
 * @param {object} queryParameters see api documentation
 * @throws Error
 */
export async function createQueryRequest(url, method, queryParameters) {
    const fetchUrl = url + objectToQueryParameters(queryParameters);

    return await createRequest(fetchUrl, {
        'method': method,
        'headers': {
            'Content-Type': 'application/json',
        }
    });
}

/**
 * @param {string} url
 * @param {string} method
 * @param {object} parameters see api documentation
 * @throws Error
 */
export async function createJsonRequest(url, method, parameters) {
    return await createRequest(url, {
        'method': method,
        'headers': {
            'Content-Type': 'application/json',
        },
        'body': JSON.stringify(parameters)
    });
}

/**
 * @param {string} url
 * @param {string} method
 * @param {object} queryParameters see api documentation
 * @param {object} bodyParameters see api documentation
 *
 * @throws Error
 */
export async function createFormRequest(url, method, bodyParameters, queryParameters) {
    const fetchUrl = url + objectToQueryParameters(queryParameters);

    return await createRequest(fetchUrl, {
        'method': method,
        'body': bodyParameters
    });
}

/**
 * @param {string} url
 * @param {object} fetchOptions
 *
 * @throws Error
 */
export async function createRequest(url, fetchOptions) {
    try {
        const options = mergeFetchOptions(fetchOptionsDefault, fetchOptions);

        return await fetch(url, options);
    }
    catch (error) {
        throw new Error(`Response error [${error}]`);
    }
}

/**
 * @typedef {object} ResponseDto
 *
 * @property {string} status
 * @property {string} message
 * @property {object} data
 * @property {object} errors
 * @property {object} headers
 */

/**
 * @param {object} response
 * @param {function(object):object} callbackResponseNoContent function(response)
 * @param {function(array):object} callbackRequestError function(response.errors)
 * @param {function(array):object} callbackResponseOk function(response.data)
 * @returns {Promise<ResponseDto>}
 * @throws Error
 */
export async function manageResponseJson(response, callbackResponseNoContent, callbackRequestError, callbackResponseOk) {

    if (!response.ok) {
        throw new Error(`Response error status ${response.status}`);
    }

    if (response.status === 204) {  // No content
        if (callbackResponseNoContent) {
            return callbackResponseNoContent(response);
        }

        return response;
    }

    try {
        const responseData = await response.json();

        if (responseData.errors.length > 0) {
            if (callbackRequestError) {
                return callbackRequestError(responseData.errors);
            }

            throw new Error('Request errors');
        }

        if (callbackResponseOk) {
            return callbackResponseOk(responseData.data);
        }

        return responseData;
    } catch (error) {
        throw new Error(`Response error [${error}]`);
    }
}

/**
 * @param {object} queryParameters
 *
 * @returns {string}
 */
function objectToQueryParameters(queryParameters) {
    const queryParametersEncoded = Object.entries(queryParameters)
        .map(([name, value]) => `${name}=${value}`)
        .join('&');

    return queryParametersEncoded !== '' ? '?' + queryParametersEncoded : '';
}

/**
 * @param {object} fetchOptionsDefault
 * @param {object} fetchOptions
 *
 * @returns {object}
 */
function mergeFetchOptions(fetchOptionsDefault, fetchOptions) {
    let options = { ...fetchOptionsDefault, ...fetchOptions };

    options.headers = { ...fetchOptionsDefault.headers, ...fetchOptions.headers };

    return options;
}