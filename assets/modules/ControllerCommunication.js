/**
 * @param {HTMLElement} controllerChild
 * @param {string} messageName
 * @param {object} content
 * @param {string} [changeControllerChildName]
 */
export function sendMessageToChildController(controllerChild, messageName, content, changeControllerChildName) {
    const eventNameFull = typeof changeControllerChildName === 'undefined'
        ? getEventName(controllerChild.dataset.controller, messageName)
        : getEventName(changeControllerChildName, messageName);

    controllerChild.dispatchEvent(new CustomEvent(eventNameFull, {
        detail: {
            content: typeof content === 'undefined' ? {} : content
        }
    }));
}

/**
 * @param {HTMLElement} controllerSender
 * @param {string} messageName
 * @param {object} content
 * @param {string} [changeControllerSenderName]
 */
export function sendMessageToParentController(controllerSender, messageName, content, changeControllerSenderName) {
    const eventNameFull = typeof changeControllerSenderName === 'undefined'
        ? getEventName(controllerSender.dataset.controller, messageName)
        : getEventName(changeControllerSenderName, messageName);

    controllerSender.dispatchEvent(new CustomEvent(eventNameFull, {
        detail: {
            content: typeof content === 'undefined' ? {} : content
        },
        bubbles: true
    }));
}

/**
 * @param {HTMLElement} controllerSender
 * @param {string} messageName
 * @param {object} content
 * @param {string} [changeControllerSenderName]
 */
export function sendMessageToNotRelatedController(controllerSender, messageName, content, changeControllerSenderName) {
    const eventNameFull = typeof changeControllerSenderName === 'undefined'
        ? getEventName(controllerSender.dataset.controller, messageName)
        : getEventName(changeControllerSenderName, messageName);

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