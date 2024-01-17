/**
 * @param {HTMLElement} controllerChild
 * @param {string} eventName
 * @param {object} content
 * @param {string} [changeControllerChildName]
 */
export function sendMessageToChildController(controllerChild, eventName, content, changeControllerChildName) {
    const eventNameFull = typeof changeControllerChildName === 'undefined'
        ? getEventName(controllerChild.dataset.controller, eventName)
        : getEventName(changeControllerChildName, eventName);

    controllerChild.dispatchEvent(new CustomEvent(eventNameFull, {
        detail: {
            content: typeof content === 'undefined' ? {} : content
        }
    }));
}

/**
 * @param {HTMLElement} controllerSender
 * @param {string} eventName
 * @param {object} content
 * @param {string} [changeControllerChildName]
 */
export function sendMessageToParentController(controllerSender, eventName, content, changeControllerSenderName) {
    const eventNameFull = typeof changeControllerSenderName === 'undefined'
        ? getEventName(controllerSender.dataset.controller, eventName)
        : getEventName(changeControllerSenderName, eventName);

    controllerSender.dispatchEvent(new CustomEvent(eventNameFull, {
        detail: {
            content: typeof content === 'undefined' ? {} : content
        },
        bubbles: true
    }));
}

/**
 * @param {HTMLElement} controllerSenderName
 * @param {string} eventName
 * @param {object} content
 * @param {string} [changeControllerChildName]
 */
export function sendMessageToNotRelatedController(controllerSenderName, eventName, content, changeControllerSenderName) {
    const eventNameFull = typeof changeControllerSenderName === 'undefined'
        ? getEventName(controllerSenderName.dataset.controller, eventName)
        : getEventName(changeControllerSenderName, eventName);

    window.dispatchEvent(new CustomEvent(eventNameFull, {
        detail: {
            content: typeof content === 'undefined' ? {} : content
        }
    }));
}

/**
 * @param {string} controllerName
 * @param {string} eventName
*/
function getEventName(controllerName, eventName) {
    return `${controllerName}:${eventName}`;
}