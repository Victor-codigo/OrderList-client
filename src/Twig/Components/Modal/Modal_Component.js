import { Controller } from '@hotwired/stimulus';
import * as event from '../../../../assets/modules/Event';

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

    #dispatchModalBeforeShowed() {
        this.contentTags.forEach((tag) => tag.dispatchEvent(new CustomEvent('ModalComponent:beforeShowed', {
            detail: {
                content: {
                    showedFirstTime: this.showedFirstTime
                }
            }
        })));
    }

    #dispatchModalShowed() {
        this.contentTags.forEach((tag) => tag.dispatchEvent(new CustomEvent('ModalComponent:showed', {
            detail: {
                content: {
                    showedFirstTime: this.showedFirstTime
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