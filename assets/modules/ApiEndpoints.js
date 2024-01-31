import * as fetch from 'App/modules/Fetch';

const API_VERSION = '1';
const API_DOMAIN = 'http://orderlist.api';
const CLIENT_DOMAIN = 'http://orderlist.client';
const GET_SHOPS_URL = `${API_DOMAIN}/api/v${API_VERSION}/shops`;
const POST_SHOP_URL = `${CLIENT_DOMAIN}/ajax/{locale}/{group_name}/shop/create`;


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
 * @returns {Promise<{
 *      'page': int,
 *      'pages_total': int,
 *      'shops': array
 * }>}
 * @throws Error
 */
export async function getShopsData(queryParameters) {
    const response = await fetch.createJsonRequest(GET_SHOPS_URL, 'GET', queryParameters);
    const responseJson = await fetch.manageResponseJson(response,
        (response) => {
            return {
                data: {
                    page: 1,
                    pages_total: 0,
                    shops: []
                }
            }
        },
        null
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
 * @param {HTMLFormElement} form
 * @param {HTMLElement} submitter
 *
 * @returns {Promise<import('App/modules/Fetch').ResponseDto>}
 */
export async function createShop(form, submitter) {
    const url = POST_SHOP_URL
        .replace('{locale}', getLocale())
        .replace('{group_name}', getGroupName())

    form.action = url;
    const formData = new FormData(form, submitter);
    const response = await fetch.createFormRequest(url, 'POST', formData, {});

    return await response.json();
}

/**
 * @returns {string}
 */
function getGroupName() {
    return window.location.pathname.split('/')[2];
}

/**
 * @returns {string}
 */
function getLocale() {
    return window.location.pathname.split('/')[1];
}

