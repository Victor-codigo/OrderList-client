export function dispatch(componentDispatcher, componentHandler, eventName, detail,) {
    const event = new CustomEvent(`${componentHandler}:${eventName}`, detail);

    componentDispatcher.dispatchEvent(event);
}

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

export function removeEventListenerDelegate(element, eventName) {
    element.removeEventListener(eventName, delegateFunction);
}

