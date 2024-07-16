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
 * @returns {string|null}
 */
export function getSection() {
    const urlPath = window.location.pathname.split('/');
    const sections = Object
        .values(SECTIONS)
        .map((section) => section.replace('_', '-'));
    const sectionsFound = urlPath.filter((urlChunk) => sections.includes(urlChunk))

    if (sectionsFound.length === 0) {
        return null;
    }

    return sectionsFound[0];
}

export const SECTIONS = {
    PRODUCT: 'product',
    SHOP: 'shop',
    ORDER: 'order',
    ORDERS: 'orders',
    LIST_ORDERS: 'list_orders',
    GROUP: 'groups',
    GROUP_USERS: 'users'
};
