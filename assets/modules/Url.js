export function getLocale() {
    return window.location.pathname.split('/')[1];
}
