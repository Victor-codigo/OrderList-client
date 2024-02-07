import { Controller } from '@hotwired/stimulus';
import BootstrapModal from 'App/modules/ModalManager/BootstrapModal';
import ModalManager from 'App/modules/ModalManager/ModalManager';

/**
 * @fires ModalComponent:beforeShowed
 * @fires ModalComponent:showed
 * @fires ModalComponent:hided
 * @fires ModalComponent:afterHided
 */
export default class Modal extends Controller {
    /**
     * @type {ModalManager}
     */
    static #modalManager;

    connect() {
        this.contentTags = this.element.querySelectorAll('[data-modal-component-content]');
        this.showedFirstTime = true;

        Modal.#modalManager = new ModalManager(new BootstrapModal());

        this.element.addEventListener('show.bs.modal', this.#dispatchModalBeforeShowed.bind(this));
        this.element.addEventListener('shown.bs.modal', this.#dispatchModalShowed.bind(this));
        this.element.addEventListener('hide.bs.modal', this.#dispatchModalHided.bind(this));
        this.element.addEventListener('hidden.bs.modal', this.#dispatchModalAfterHided.bind(this));
        this.element.addEventListener('click', this.#handleModalClosedByEscOrClickedOut.bind(this));
        this.element.addEventListener('keydown', this.#handleModalClosedByEscOrClickedOut.bind(this));
    }

    disconnect() {
        this.element.removeEventListener('show.bs.modal', this.#dispatchModalBeforeShowed);
        this.element.removeEventListener('shown.bs.modal', this.#dispatchModalShowed);
        this.element.removeEventListener('hide.bs.modal', this.#dispatchModalBeforeShowed);
        this.element.removeEventListener('hidden.bs.modal', this.#dispatchModalBeforeShowed);
        this.element.removeEventListener('click', this.#handleModalClosedByEscOrClickedOut);
        this.element.removeEventListener('keydown', this.#handleModalClosedByEscOrClickedOut);
    }

    /**
     * @param {MouseEvent|KeyboardEvent} event
     */
    #handleModalClosedByEscOrClickedOut(event) {
        if (event instanceof KeyboardEvent && event.key !== 'Escape') {
            return;
        }

        if (event instanceof MouseEvent && event.target !== this.element) {
            return;
        }

        Modal.#modalManager.close();
    }

    /**
     * @param {MouseEvent} event
     */
    #dispatchModalBeforeShowed(event) {
        /**
         * @event ModalComponent:beforeShowed
         * @type {object}
         * @property {boolean} showedFirstTime
         */
        this.contentTags.forEach((tag) => tag.dispatchEvent(new CustomEvent('ModalComponent:beforeShowed', {
            detail: {
                content: {
                    showedFirstTime: this.showedFirstTime,
                    triggerElement: event.relatedTarget,
                    modalManager: Modal.#modalManager
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
                    modalManager: Modal.#modalManager
                }
            }
        })));

        this.showedFirstTime = false;
    }

    /**
     * @param {Event} event
     */
    #dispatchModalHided(event) {
        this.contentTags.forEach((tag) => tag.dispatchEvent(new CustomEvent('ModalComponent:hided')), {
            modalManager: Modal.#modalManager
        });
    }

    /**
     * @param {Event} event
     */
    #dispatchModalAfterHided(event) {
        this.contentTags.forEach((tag) => tag.dispatchEvent(new CustomEvent('ModalComponent:afterHided')), {
            modalManager: Modal.#modalManager
        });
    }
}