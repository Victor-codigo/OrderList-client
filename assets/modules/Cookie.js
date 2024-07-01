/**
 * @param {string} name
 * @returns {string|null}
 */
export function getCookie(name) {
    const cookies = `; ${document.cookie}`;
    const cookiesArray = cookies.split(`; ${name}=`);

    if (cookiesArray.length !== 2) {
        return null;
    }

    return cookiesArray
        .pop()
        .split(';')
        .shift();
}