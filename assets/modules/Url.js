/**
 * @returns {string}
 */
export function getLocale() {
    return window.location.pathname.split('/')[1];
}

/**
 * @returns {string}
 */
export function getGroupName() {
    return window.location.pathname.split('/')[2];
}

/**
 * @returns {string}
 */
export function getSection() {
    const urlPath = window.location.pathname.split('/');

    if (urlPath[2] == 'group') {
        return 'group';
    }

    return window.location.pathname.split('/')[3];
}

/**
 * @returns {string}
 */
export function getSubSection() {
    return window.location.pathname.split('/')[4];
}

export const SECTIONS = {
    PRODUCT: 'product',
    SHOP: 'shop',
    ORDER: 'order',
    ORDERS: 'orders',
    LIST_ORDERS: 'list_orders',
    GROUP: 'group',
    GROUP_USERS: 'users'
};
