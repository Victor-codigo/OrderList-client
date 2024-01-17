import * as cookies from './Cookie';

const COOKIE_TOKENSESSION_NAME = 'TOKENSESSION';
const API_VERSION = '1';
const API_DOMAIN = 'http://orderlist.api';
const GET_SHOPS_URL = `${API_DOMAIN}/api/v${API_VERSION}/shops`;


/**
 * @param {string} endpointName
 * @param {Object.<string, string>} queryParameters see api documentation
 */
export async function executeEndPointByName(endpointName, queryParameters) {
    const endpointsNames = {
        'getShopsData': getShopsData,
        'getShopsNames': getShopsNames
    };

    if (typeof endpointsNames[endpointName] === 'undefined') {
        throw new Error('ApiEndPoint: endpoint does not exist');
    }

    return await endpointsNames[endpointName](queryParameters);
}

/**
 * @param {object} queryParameters see api documentation
 * @returns {
 *      'page': int,
 *      'pages_total': int,
 *      'shops': array
 * }
 * @throws Error
 */
export async function getShopsData(queryParameters) {
    const response = await createJsonRequest(GET_SHOPS_URL, 'GET', queryParameters);
    const responseJson = await manageResponseJson(response,
        (response) => {
            return {
                'data': {
                    'page': 1,
                    'pages_total': 0,
                    'shops': []
                }
            }
        }
    );

    return responseJson.data;
}

/**
* @param {object} queryParameters see api documentation
* @returns string[]
* @throws Error
*/
export async function getShopsNames(queryParameters) {
    const responseData = await getShopsData(queryParameters);

    return responseData.shops.map((shop) => shop.name);
}

/**
 * @param {string} url
 * @param {string} method
 * @param {object} queryParameters see api documentation
 * @throws Error
 */
async function createJsonRequest(url, method, queryParameters) {
    try {
        const tokenSession = cookies.getCookie(COOKIE_TOKENSESSION_NAME);
        const queryParametersEncoded = Object.entries(queryParameters)
            .map(([name, value]) => `${name}=${value}`)
            .join('&');

        return await fetch(`${url}?${queryParametersEncoded}`, {
            'credentials': 'same-origin',
            'method': method,
            'mode': 'cors',
            'headers': {
                'Content-Type': 'application/json',
                'Authorization': `bearer ${tokenSession}`
            },

        });
    }
    catch (error) {
        throw new Error(`Response error [${error}]`);
    }
}

/**
 * @param {object} response
 * @param {function(object):object} callbackResponseNoContent function(response)
 * @param {function(array):object} callbackRequestError function(response.errors)
 * @returns {object}
 * @throws Error
 */
async function manageResponseJson(response, callbackResponseNoContent, callbackRequestError) {

    if (!response.ok) {
        throw new Error(`Response error status ${response.status}`);
    }

    if (response.status === 204) {  // No content
        if (callbackResponseNoContent) {
            return callbackResponseNoContent(response);
        }

        return {};
    }

    try {
        const responseData = await response.json();

        if (responseData.errors.length > 0) {
            if (callbackRequestError) {
                return callbackRequestError(responseData.errors);
            }

            throw new Error('Request errors');
        }

        return responseData;
    } catch (error) {
        throw new Error(`Response error [${error}]`);
    }
}