import { Controller } from '@hotwired/stimulus';
import * as communication from '/assets/modules/ControllerCommunication';

export default class extends Controller {

    connect() {
        this.itemsIdSelected = [];
        this.listItemsCheckboxes = this.element.querySelectorAll('[data-js-checkbox]');
        this.removeMultiButtonTag = this.element.querySelector('[data-js-form-remove-many-items-button]');
        this.element.addEventListener('change', this.#itemsIdSelectedToggle.bind(this));

        this.#itemsIdSelectedAddAll();
        this.#buttonRemoveMultiToggle();
    }

    disconnect() {
        this.element.removeEventListener('change', this.#itemsIdSelectedToggle);
    }

    #buttonRemoveMultiToggle() {
        if (this.itemsIdSelected.length === 0) {
            this.removeMultiButtonTag.disabled = true;

            return;
        }

        this.removeMultiButtonTag.disabled = false;
    }

    #itemsIdSelectedToggle(event) {
        if (event.target.tagName !== 'input' && event.target.type !== 'checkbox') return;

        const listItem = event.target.closest('[data-item-id]');

        if (!event.target.checked) {
            this.itemsIdSelected = this.itemsIdSelected.filter((item) => item.id !== listItem.dataset.itemId);
            this.#buttonRemoveMultiToggle();

            return;
        }

        this.itemsIdSelected.push({
            'id': listItem.dataset.itemId,
            'name': listItem.dataset.itemName
        });
        this.#buttonRemoveMultiToggle();
    }

    #itemsIdSelectedAddAll() {
        this.listItemsCheckboxes.forEach((checkbox) => {
            const listItem = checkbox.closest('[data-item-id]');

            if (checkbox.checked) {
                this.itemsIdSelected.push({
                    'id': listItem.dataset.itemId,
                    'name': listItem.dataset.itemName
                });
            }
        });
    }

    sendMessageHomeSectionRemoveMultiToParent() {
        communication.sendMessageToParentController(this.element, 'homeSectionRemoveMulti', {
            items: this.itemsIdSelected
        });
    }

}
