export function getLocale() {
    return window.location.pathname.split('/')[1];
}

/**
 * @returns {string}
 */
export function getGroupName() {
    return window.location.pathname.split('/')[2];
}

export function getSection() {
    return window.location.pathname.split('/')[3];
}

export const SECTIONS = {
    PRODUCT: 'product',
    SHOP: 'shop',
    ORDER: 'order'
};
