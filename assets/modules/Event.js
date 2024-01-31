/**
 * @param {HTMLElement} componentDispatcher
 * @param {string} componentHandler
 * @param {string} eventName
 * @param {object} detail
 */
export function dispatch(componentDispatcher, componentHandler, eventName, detail) {
    const eventComponentName = componentHandler === "" ? "" : `${componentHandler}:`

    const event = new CustomEvent(`${eventComponentName}${eventName}`, detail);

    componentDispatcher.dispatchEvent(event);
}

/**
 * @callback callbackListener
 * @param {HTMLElement} elementTargetEvent
 * @param {Event} event
 */
/**
 * @param {Object} delegate
 * @param {HTMLElement} delegate.element
 * @param {string} delegate.elementDelegateSelector
 * @param {string} delegate.eventName
 * @param {callbackListener} delegate.callbackListener
 * @param {(boolean|AddEventListenerOptions)} delegate.eventOptions
 */
export function addEventListenerDelegate({ element, elementDelegateSelector, eventName, callbackListener, eventOptions }) {
    let delegateFunction = (event) => {
        const elementTargetEvent = event.target.closest(elementDelegateSelector);

        if (elementTargetEvent === null) {
            return;
        }

        callbackListener(elementTargetEvent, event);
    };

    element.addEventListener(eventName, delegateFunction, eventOptions);
}


/**
 * @param {HTMLElement} element
 * @param {string} eventName
 * @param {callbackListener} delegateFunction
 */
export function removeEventListenerDelegate(element, eventName, delegateFunction) {
    element.removeEventListener(eventName, delegateFunction);
}

