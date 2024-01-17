/**
 * @param {HTMLElement} componentDispatcher
 * @param {string} componentHandler
 * @param {string} eventName
 * @param {*} detail
 */
export function dispatch(componentDispatcher, componentHandler, eventName, detail,) {
    const eventComponentName = componentHandler === "" ? "" : `${componentHandler}:`

    const event = new CustomEvent(`${eventComponentName}${eventName}`, detail);

    componentDispatcher.dispatchEvent(event);
}

/**
 * @param {Object} delegate
 * @param {HTMLElement} delegate.element
 * @param {string} delegate.elementDelegateSelector
 * @param {string} delegate.eventName
 * @param {callback} delegate.callbackListener
 * @param {(boolean|AddEventListenerOptions|Array)} delegate.eventOptions
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
 */
export function removeEventListenerDelegate(element, eventName) {
    element.removeEventListener(eventName, delegateFunction);
}

