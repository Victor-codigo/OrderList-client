
export function encodeUrlParameter(parameterValue) {
    'use strict'
    return parameterValue
        .replaceAll(' ', '-')
        .toLowerCase();
}

export function decodeUrlParameter(parameterValue) {
    'use strict'
    return parameterValue
        .replaceAll(' ', '')
        .replaceAll('-', ' ')
        .toLowerCase();
}