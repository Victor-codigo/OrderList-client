export const MODAL_CHAINS = {
    productCreateChain: {
        name: 'productCreateChain',
        modals: {
            productCreate: 'product_create_modal',
            shopList: 'shop_list_select_modal',
            shopCreate: 'shop_create_modal'
        }
    },

    productModifyChain: {
        name: 'productModifyChain',
        modals: {
            productModify: 'product_modify_modal',
            shopList: 'shop_list_select_modal',
            shopCreate: 'shop_create_modal'
        }
    }
}

/**
 * @typedef {Object} ItemData
 * @property {string} id
 * @property {string} name
 * @property {string} description
 * @property {string} image
 * @property {ItemShopData[]} shops
 */

/**
 * @typedef {Object} ItemShopData
 * @property {string} id
 * @property {string} name
 * @property {string} description
 * @property {string} image
 * @property {number} price
 */