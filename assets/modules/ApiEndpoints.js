import * as fetch from 'App/modules/Fetch';
import * as url from 'App/modules/Url';

const API_VERSION = '1';
const API_DOMAIN = 'http://orderlist.api';
const CLIENT_DOMAIN = 'http://orderlist.client';

const GET_SHOPS_URL = `${API_DOMAIN}/api/v${API_VERSION}/shops`;
const GET_PRODUCTS_URL = `${API_DOMAIN}/api/v${API_VERSION}/products`;
const GET_PRODUCTS_SHOPS_PRICE_URL = `${API_DOMAIN}/api/v${API_VERSION}/products/price`;
const GET_LIST_ORDERS_URL = `${API_DOMAIN}/api/v${API_VERSION}/list-orders`;
const GET_LIST_ORDERS_PRICE = `${API_DOMAIN}/api/v${API_VERSION}/list-orders/price`;
const PATCH_ORDER_BOUGHT = `${API_DOMAIN}/api/v${API_VERSION}/orders/bought`;

const POST_SHOP_URL = `${CLIENT_DOMAIN}/ajax/{locale}/{group_name}/shop/create`;
const POST_PRODUCT_URL = `${CLIENT_DOMAIN}/ajax/{locale}/{group_name}/product/create`;


/**
 * @param {string} endpointName
 * @param {Object.<string, string>} queryParameters see api documentation
 */
export async function executeEndPointByName(endpointName, queryParameters) {
    const endpointsNames = {
        'getShopsData': getShopsData,
        'getShopsNames': getShopsNames,
        'getProductsData': getProductsData,
        'getProductsNames': getProductsNames,
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
    const response = await fetch.createQueryRequest(GET_SHOPS_URL, 'GET', queryParameters);
    const responseJson = await fetch.manageResponseJson(response,
        (responseDataNoContent) => {
            return {
                data: {
                    page: 1,
                    pages_total: 0,
                    shops: []
                }
            }
        },
        null,
        null
    );

    return responseJson.data;
}

/**
* @param {object} queryParameters see api documentation
* @returns {Promise<string[]>}
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
    const createShopUrl = POST_SHOP_URL
        .replace('{locale}', url.getLocale())
        .replace('{group_name}', url.getGroupName())

    form.action = createShopUrl;
    const formData = new FormData(form, submitter);
    const response = await fetch.createFormRequest(createShopUrl, 'POST', formData, {});

    return await response.json();
}

/**
 * @param {object} queryParameters see api documentation
 * @returns {Promise<{
*      'page': int,
*      'pages_total': int,
*      'products': array
* }>}
* @throws Error
*/
export async function getProductsData(queryParameters) {
    const response = await fetch.createQueryRequest(GET_PRODUCTS_URL, 'GET', queryParameters);
    const responseJson = await fetch.manageResponseJson(response,
        (responseDataNoContent) => {
            return {
                data: {
                    page: 1,
                    pages_total: 0,
                    products: []
                }
            }
        },
        null,
        null
    );

    return responseJson.data;
}

/**
* @param {object} queryParameters see api documentation
* @returns {Promise<string[]>}
* @throws Error
*/
export async function getProductsNames(queryParameters) {
    const responseData = await getProductsData(queryParameters);

    return responseData.products.map((product) => product.name);
}

/**
 * @param {HTMLFormElement} form
 * @param {HTMLElement} submitter
 *
 * @returns {Promise<import('App/modules/Fetch').ResponseDto>}
 */
export async function createProduct(form, submitter) {
    const createShopUrl = POST_PRODUCT_URL
        .replace('{locale}', url.getLocale())
        .replace('{group_name}', url.getGroupName());

    form.action = createShopUrl;
    const formData = new FormData(form, submitter);
    const response = await fetch.createFormRequest(createShopUrl, 'POST', formData, {});

    return await response.json();
}

/**
 * @param {string} groupId
 * @param {string[]} productsId
 * @param {string[]} shopsId
 *
 * @returns {Promise<{
*      'page': number,
*      'pages_total': number,
*      'products_shops_prices': object[]
* }>}
* @throws Error
*/
export async function getProductShopsPricesData(groupId, productsId, shopsId) {
    const queryParameters = {
        'group_id': groupId,
        'products_id': productsId.join(','),
        'shops': shopsId.join(',')
    };
    const response = await fetch.createQueryRequest(GET_PRODUCTS_SHOPS_PRICE_URL, 'GET', queryParameters);
    const responseJson = await fetch.manageResponseJson(response,
        (responseDataNoContent) => {
            return {
                data: {
                    page: 1,
                    pages_total: 0,
                    products_shops_prices: []
                }
            }
        },
        null,
        (responseDataOk) => {
            return {
                data: {
                    page: 1,
                    pages_total: 1,
                    products_shops_prices: responseDataOk
                }
            }
        }
    );

    return responseJson.data;
}

export async function getListOrdersData(queryParameters) {
    const response = await fetch.createQueryRequest(GET_LIST_ORDERS_URL, 'GET', queryParameters);
    const responseJson = await fetch.manageResponseJson(response,
        (responseDataNoContent) => {
            return {
                data: {
                    page: 1,
                    pages_total: 0,
                    listOrders: []
                }
            }
        },
        null,
        null
    );

    responseJson.data['listOrders'] = responseJson.data.list_orders;
    delete responseJson.data.list_orders;

    return responseJson.data;
}

/**
* @param {object} queryParameters see api documentation
* @returns {Promise<string[]>}
* @throws Error
*/
export async function getListOrdersNames(queryParameters) {
    const responseData = await getListOrdersData(queryParameters);

    return responseData.listOrders.map((listOrders) => listOrders.name);
}

/**
 * @param {string} orderId
 * @param {string} groupId
 * @param {boolean} bought
 *
 * @return {Promise<boolean>}
 *
 * @throws {Error}
 */
export async function orderBought(orderId, groupId, bought) {
    const parameters = {
        'order_id': orderId,
        'group_id': groupId,
        'bought': bought
    };

    const response = await fetch.createJsonRequest(PATCH_ORDER_BOUGHT, 'PATCH', parameters);
    const responseJson = await fetch.manageResponseJson(response,
        null,
        (responseDataError) => {
            return {
                data: {},
                errors: responseDataError
            }
        },
        (responseDataOk) => {
            return {
                data: responseDataOk,
                errors: []
            }
        }
    );

    if (responseJson.errors.length > 0) {
        throw new Error('Error marking order as bought');
    }

    return true;
}

/**
 * @param {string} listOrdersId
 * @param {string} groupId
 *
 * @return {Promise<{total: number, bought: number}>}
 *
 * @throws {Error}
 */
export async function getListOrdersPrice(listOrdersId, groupId) {
    const parameters = {
        'list_orders_id': listOrdersId,
        'group_id': groupId,
    };

    const response = await fetch.createQueryRequest(GET_LIST_ORDERS_PRICE, 'GET', parameters);
    const responseJson = await fetch.manageResponseJson(response,
        (responseDataNoContent) => {
            return {
                data: {
                    'total': 0,
                    'bought': 0
                },
                errors: []
            }
        },
        (responseDataError) => {
            return {
                data: {},
                errors: responseDataError
            }
        },
        (responseDataOk) => {
            return {
                data: responseDataOk,
                errors: []
            }
        }
    );

    if (responseJson.errors.length > 0) {
        throw new Error('Error getting list of orders price');
    }

    return responseJson.data;
}