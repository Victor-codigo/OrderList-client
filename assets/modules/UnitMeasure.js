/**
 * @param {string} unit
 *
 * @returns {string}
 */
export function parseApiUnits(unit) {
    const units = {
        "UNITS": "Unit",
        "KG": "Kg"
    };

    return typeof units[unit] === 'undefined' ? unit.toLowerCase() : units[unit];
}