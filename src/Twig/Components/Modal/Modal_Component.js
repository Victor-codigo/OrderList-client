import { Controller } from '@hotwired/stimulus';

/**
 * @event ModalComponent:beforeShowed
 * @event ModalComponent:showed
 * @event ModalComponent:hided
 * @event ModalComponent:afterHided
 */
export default class extends Controller {
    connect() {
        this.contentTags = this.element.querySelectorAll('[data-modal-component-content]');
        this.showedFirstTime = true;

        this.element.addEventListener('show.bs.modal', this.#dispatchModalBeforeShowed.bind(this));
        this.element.addEventListener('shown.bs.modal', this.#dispatchModalShowed.bind(this));
        this.element.addEventListener('hide.bs.modal', this.#dispatchModalHided.bind(this));
        this.element.addEventListener('hidden.bs.modal', this.#dispatchModalAfterHided.bind(this));

    }

    disconnect() {
        this.element.removeEventListener('show.bs.modal', this.#dispatchModalBeforeShowed);
        this.element.removeEventListener('shown.bs.modal', this.#dispatchModalShowed);
        this.element.removeEventListener('hide.bs.modal', this.#dispatchModalBeforeShowed);
        this.element.removeEventListener('hidden.bs.modal', this.#dispatchModalBeforeShowed);
    }


    /**
     * @param {HTMLElement|null} triggerElement
     * @returns {{
     *      modalBefore: string,
     *      controllerModalEventHandler: string
     * }}
     */
    #getTriggerElementData(triggerElement) {
        let modalBefore = null;
        let controllerModalEventHandler = null;

        if (triggerElement === null) {
            return {
                modalBefore: null,
                controllerModalEventHandler: null
            };
        }

        if (typeof triggerElement.dataset.modalCurrent !== 'undefined') {
            modalBefore = triggerElement.dataset.modalCurrent;
        }

        if (typeof triggerElement.dataset.eventControllerHandler !== 'undefined') {
            controllerModalEventHandler = triggerElement.dataset.eventControllerHandler;
        }

        return {
            modalBefore: modalBefore,
            controllerModalEventHandler: controllerModalEventHandler
        };
    }

    /**
     * @param {MouseEvent} event
     */
    #dispatchModalBeforeShowed(event) {
        this.contentTags.forEach((tag) => tag.dispatchEvent(new CustomEvent('ModalComponent:beforeShowed', {
            detail: {
                content: {
                    showedFirstTime: this.showedFirstTime,
                    triggerElement: event.relatedTarget,
                    triggerElementData: this.#getTriggerElementData(event.relatedTarget)
                }
            }
        })));
    }

    /**
     * @param {MouseEvent} event
     */
    #dispatchModalShowed(event) {
        this.contentTags.forEach((tag) => tag.dispatchEvent(new CustomEvent('ModalComponent:showed', {
            detail: {
                content: {
                    showedFirstTime: this.showedFirstTime,
                    triggerElement: event.relatedTarget,
                    triggerElementData: this.#getTriggerElementData(event.relatedTarget)
                }
            }
        })));

        this.showedFirstTime = false;
    }

    #dispatchModalHided() {
        this.contentTags.forEach((tag) => tag.dispatchEvent(new CustomEvent('ModalComponent:hided')));
    }

    #dispatchModalAfterHided() {
        this.contentTags.forEach((tag) => tag.dispatchEvent(new CustomEvent('ModalComponent:afterHided')));
    }
}