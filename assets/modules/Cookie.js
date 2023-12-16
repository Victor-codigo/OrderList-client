export function getCookie(name) {
    const cookies = `; ${document.cookie}`;
    const cookiesArray = cookies.split(`; ${name}=`);

    if (cookiesArray.length !== 2) {
        throw new Error('Cookie name not found');
    }

    return cookiesArray
        .pop()
        .split(';')
        .shift();
}