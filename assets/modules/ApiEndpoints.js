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
 * @param {string} groupId
 * @param {string[]|null} shopsId
 * @param {string[]|null} productsId
 * @param {string|null} shopName
 * @param {number} page
 * @param {number} pageItems
 * @param {string|null} shopNameFilterType
 * @param {string|null} shopNameFilterValue
 * @param {boolean|null} orderAsc
 *
 * @returns {Promise<{
 *      'page': int,
 *      'pages_total': int,
 *      'shops': array
 * }>}
 * @throws Error
 */
export async function getShopsData(groupId, page, pageItems, shopsId = null, productsId = null, shopName = null, shopNameFilterType = null, shopNameFilterValue = null, orderAsc = true) {
    const queryParameters = {
        'group_id': groupId,
        'shops_id': shopsId,
        'products_id': productsId,
        'shop_name': shopName,
        'page': page,
        'page_items': pageItems,
        'shop_name_filter_type': shopNameFilterType,
        'shop_name_filter_value': shopNameFilterValue,
        'order_asc': orderAsc
    };
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
 * @param {string} groupId
 * @param {string[]|null} shopsId
 * @param {string[]|null} productsId
 * @param {string|null} shopName
 * @param {number} page
 * @param {number} pageItems
 * @param {string|null} shopNameFilterType
 * @param {string|null} shopNameFilterValue
 * @param {boolean|null} orderAsc
 *
 * @returns {Promise<string[]>}
 * @throws Error
 */
export async function getShopsNames(groupId, page, pageItems, shopsId = null, productsId = null, shopName = null, shopNameFilterType = null, shopNameFilterValue = null, orderAsc = true) {
    const responseData = await getShopsData(
        groupId,
        page,
        pageItems,
        shopsId,
        productsId,
        shopName,
        shopNameFilterType,
        shopNameFilterValue,
        orderAsc
    );

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
 * @param {string} groupId
 * @param {string[]|null} shopsId
 * @param {string[]|null} productsId
 * @param {string|null} productName
 * @param {number} page
 * @param {number} pageItems
 * @param {string|null} productNameFilterType
 * @param {string|null} productNameFilterValue
 * @param {string|null} shopNameFilterType
 * @param {string|null} shopNameFilterValue
 * @param {boolean|null} orderAsc
 *
 * @returns {Promise<{
 *      'page': int,
 *      'pages_total': int,
 *      'products': array
 * }>}
 * @throws Error
 */
export async function getProductsData(groupId, page, pageItems, shopsId = null, productsId = null, productName = null, productNameFilterType = null, productNameFilterValue = null, shopNameFilterType = null, shopNameFilterValue = null, orderAsc = true) {
    const queryParameters = {
        'group_id': groupId,
        'shops_id': shopsId,
        'products_id': productsId,
        'product_name': productName,
        'page': page,
        'page_items': pageItems,
        'product_name_filter_type': productNameFilterType,
        'product_name_filter_value': productNameFilterValue,
        'shop_name_filter_type': shopNameFilterType,
        'shop_name_filter_value': shopNameFilterValue,
        'order_asc': orderAsc
    };
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
 * @param {string} groupId
 * @param {string[]|null} shopsId
 * @param {string[]|null} productsId
 * @param {string|null} productName
 * @param {number} page
 * @param {number} pageItems
 * @param {string|null} productNameFilterType
 * @param {string|null} productNameFilterValue
 * @param {string|null} shopNameFilterType
 * @param {string|null} shopNameFilterValue
 * @param {boolean|null} orderAsc
 *
 * @returns {Promise<string[]>}
 * @throws Error
 */
export async function getProductsNames(groupId, page, pageItems, shopsId = null, productsId = null, productName = null, productNameFilterType = null, productNameFilterValue = null, shopNameFilterType = null, shopNameFilterValue = null, orderAsc = true) {
    const responseData = await getProductsData(
        groupId,
        page,
        pageItems,
        shopsId,
        productsId,
        productName,
        productNameFilterType,
        productNameFilterValue,
        shopNameFilterType,
        shopNameFilterValue,
        orderAsc
    );

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

/**
 * @param {string} groupId
 * @param {number} page
 * @param {number} pageItems
 * @param {string[]} ordersId
 * @param {string} listOrdersId
 * @param {string|null} filterSection
 * @param {string|null} filterText
 * @param {string|null} filterValue
 * @param {boolean|null} orderAsc
 *
 * @returns {Promise<string[]>}
 * @throws Error
 */
export async function getListOrdersData(groupId, page, pageItems, ordersId = null, listOrdersId = null, filterSection = null, filterText = null, filterValue = null, orderAsc = true) {
    const queryParameters = {
        'group_id': groupId,
        'orders_id': ordersId,
        'list_orders_id': listOrdersId,
        'page': page,
        'page_items': pageItems,
        'filter_section': filterSection,
        'filter_text': filterText,
        'filter_value': filterValue,
        'order_asc': orderAsc
    };
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
* @param {string} groupId
 * @param {number} page
 * @param {number} pageItems
 * @param {string[]} ordersId
 * @param {string} listOrdersId
 * @param {string|null} filterSection
 * @param {string|null} filterText
 * @param {string|null} filterValue
 * @param {boolean|null} orderAsc
 *
* @returns {Promise<string[]>}
* @throws Error
*/
export async function getListOrdersNames(groupId, page, pageItems, ordersId = null, listOrdersId = null, filterSection = null, filterText = null, filterValue = null, orderAsc = true) {
    const responseData = await getListOrdersData(
        groupId,
        page,
        pageItems,
        ordersId,
        listOrdersId,
        filterSection,
        filterText,
        filterValue,
        orderAsc
    );

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