import * as config from 'App/Config';
import * as url from 'App/modules/Url';

/**
 * @param {number|null} price
 *
 * @returns {string}
 */
export function formatToStringLocaleCurrency(price) {
    if (price === null) {
        return '--- ' + config.CURRENCY;
    }

    const priceFormat = Math.round(price * 100) / 100;

    return priceFormat.toLocaleString(getLocaleItl(url.getLocale())) + config.CURRENCY;
}

/**
 * @param {number|null} price
 * @param {string} unit
 *
 * @returns {string}
 */
export function formatPriceCurrencyAndUnit(price, unit) {
    const unitFormatted = config.UNIT_MEASURE.translate(unit, false);
    let priceFormat = '---';
    let unitTextFormatted = '';

    if (price !== null) {
        priceFormat = (Math.round(price * 100) / 100).toString();
    }

    if (unitFormatted !== '') {
        unitTextFormatted = '/' + unitFormatted;
    }

    return `${priceFormat}${config.CURRENCY}${unitTextFormatted}`
}

/**
 * @param {number} amount
 * @param {string} unit
 *
 * @return {string}
 */
export function formatAmountAndUnit(amount, unit) {
    return `${amount.toLocaleString(getLocaleItl(url.getLocale()))} ${formatApiUnits(unit, amount > 1)}`
}

/**
 * @param {string} date
 *
 * @returns {string}
 */
export function formatDateToLocale(date) {
    return new Date(date).toLocaleDateString(
        getLocaleItl(url.getLocale()),
        config.dateFormat
    );
}

/**
 * @param {string|null} unit
 * @param {boolean} plural
 *
 * @returns {string}
 *
 */
function formatApiUnits(unit, plural) {
    if (null === unit) {
        return '';
    }

    const units = {
        "UNITS": config.UNIT_MEASURE.translate(unit, plural),
        "KG": "Kg"
    };

    return typeof units[unit] === 'undefined' ? unit.toLowerCase() : units[unit];
}

/**
 * @param {string} locale
 *
 * @returns {string}
 */
function getLocaleItl(locale) {
    const localDictionary = {
        en: 'en-US',
        es: 'es-ES',
        default: 'en-US'
    };

    if (typeof localDictionary[locale] === 'undefined') {
        return localDictionary.default;
    }

    return localDictionary[locale];
}
