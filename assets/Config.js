import * as url from 'App/modules/Url';

export const MODAL_CHAINS = {
    productCreateChain: {
        name: 'productCreateChain',
        modals: {
            productCreate: {
                name: 'productCreate',
                modalId: 'product_create_modal',
                open: {
                    shopList: 'shop_list_select_modal'
                }
            },
            shopList: {
                name: 'shopList',
                modalId: 'shop_list_select_modal',
                open: {
                    shopCreate: 'shop_create_modal'
                }
            },
            shopCreate: {
                name: 'shopCreate',
                modalId: 'shop_create_modal',
                open: {
                    shopCreated: 'product_create_modal'
                }
            }
        }
    },

    productModifyChain: {
        name: 'productModifyChain',
        modals: {
            productModify: {
                name: 'productModify',
                modalId: 'product_modify_modal',
                open: {
                    shopList: 'shop_list_select_modal'
                }
            },
            shopList: {
                name: 'shopList',
                modalId: 'shop_list_select_modal',
                open: {
                    shopCreate: 'shop_create_modal'
                }
            },
            shopCreate: {
                name: 'shopCreate',
                modalId: 'shop_create_modal',
                open: {
                    shopCreated: 'product_modify_modal'
                }
            }
        }
    },

    shopCreateChain: {
        name: 'shopCreateChain',
        modals: {
            shopCreate: {
                name: 'shopCreate',
                modalId: 'shop_create_modal',
                open: {
                    productsListModal: 'product_list_select_modal'
                }
            },
            productList: {
                name: 'productList',
                modalId: 'product_list_select_modal',
                open: {
                    productCreate: 'product_create_modal'
                }
            },
            productCreate: {
                name: 'productCreate',
                modalId: 'product_create_modal',
                open: {
                    productCreated: 'shop_create_modal'
                }
            }
        }
    },

    shopModifyChain: {
        name: 'shopModifyChain',
        modals: {
            shopModify: {
                name: 'shopModify',
                modalId: 'shop_modify_modal',
                open: {
                    productsListModal: 'product_list_select_modal'
                }
            },
            productList: {
                name: 'productList',
                modalId: 'product_list_select_modal',
                open: {
                    productCreate: 'product_create_modal'
                }
            },
            productCreate: {
                name: 'productCreate',
                modalId: 'product_create_modal',
                open: {
                    productCreated: 'shop_modify_modal'
                }
            }
        }
    },

    orderCreateChain: {
        name: 'orderCreateChain',
        modals: {
            orderCreate: {
                name: 'order_create',
                modalId: 'order_create_modal',
                open: {
                    productsListModal: 'order_product_list_select_modal'
                }
            },
            orderProductList: {
                name: 'productList',
                modalId: 'order_product_list_select_modal',
            }
        }
    },

    orderModifyChain: {
        name: 'orderModifyChain',
        modals: {
            orderModify: {
                name: 'order_modify',
                modalId: 'order_modify_modal',
                open: {
                    productsListModal: 'order_product_list_select_modal'
                }
            },
            orderProductList: {
                name: 'productList',
                modalId: 'order_product_list_select_modal',
            }
        }
    },

    listOrdersCreateFromChain: {
        name: 'listOrdersCreateFromChain',
        modals: {
            listOrdersCreateFrom: {
                name: 'listOrdersCreateFrom',
                modalId: 'list_orders_create_from_modal',
                open: {
                    listOrdersListModal: 'list_orders_list_select_modal'
                }
            },
            listOrdersList: {
                name: 'listOrdersList',
                modalId: 'list_orders_list_select_modal',
            }
        }
    },
};

/**
 * @type {Intl.DateTimeFormatOptions}
 */
export const dateFormat = {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
};

/**
 * @type {Intl.DateTimeFormatOptions}
 */
export const dateTimeFormat = {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: false
};

/**
 * @type {string}
 */
export const CURRENCY = '€';

export const UNIT_MEASURE = {
    /**
     * @param {string} unit
     * @param {boolean} plural
     * @returns {string}
     */
    translate: (unit, plural) => {
        if (typeof UNIT_MEASURE[unit] === 'undefined') {
            return unit;
        }

        if (unit !== UNIT_MEASURE.UNITS) {
            return UNIT_MEASURE[unit];
        }

        const locale = url.getLocale();

        if (locale === 'en') {
            return plural ? 'Units' : 'Unit';
        }

        return plural ? 'Unidades' : 'Unidad';
    },

    UNITS: 'UNITS',

    KG: 'Kg',
    G: 'g',
    CG: 'cg',

    M: 'm',
    DM: 'dm',
    CM: 'cm',
    MM: 'mm',

    L: 'l',
    DL: 'dl',
    CL: 'dl',
    ML: 'ml',
};


/**
 * @typedef {Object} ItemData
 * @class
 * @property {string} id
 * @property {string} name
 * @property {string} description
 * @property {string} image
 * @property {boolean} noImage
 * @property {string} createdOn
 * @property {ItemPriceData[]} itemsPrices
 */
/**
 * @typedef {Object} ItemPriceData
 * @property {string} id
 * @property {string} name
 * @property {string} description
 * @property {string} image
 * @property {number} price
 * @property {string} unit
 */

/**
 * @typedef {object} OrderData
 * @class
 * @property {string} id
 * @property {string} name
 * @property {string|null} description
 * @property {float} amount
 * @property {boolean} bought
 * @property {string} image
 * @property {string} createdOn
 * @property {OrderDataProduct} product
 * @property {OrderDataShop} shop
 * @property {OrderDataProductShop} productShop
 */

/**
 * @typedef {object} OrderDataProduct
 * @class
 * @property {string} id
 * @property {string} name
 * @property {string|null} description
 */

/**
 * @typedef {object} OrderDataShop
 * @class
 * @property {string|null} id
 * @property {string|null} name
 * @property {string|null} description
 */

/**
 * @typedef {object} OrderDataProductShop
 * @class
 * @property {float|null} price
 * @property {string|null} unit
 */


