export function dispatch(componentDispatcher, componentHandler, eventName, detail,) {
    const event = new CustomEvent(`${componentHandler}:${eventName}`, detail);

    componentDispatcher.dispatchEvent(event);
}