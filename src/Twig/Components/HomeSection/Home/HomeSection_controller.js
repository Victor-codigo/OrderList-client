import { Controller } from '@hotwired/stimulus';
import * as communication from 'App/modules/ControllerCommunication';

export default class extends Controller {

    /**
     * @type {boolean}
     */
    interactive;

    connect() {
        this.itemsIdSelected = [];
        this.listItemsCheckboxes = this.element.querySelectorAll('[data-js-checkbox]');
        this.removeMultiButtonTag = this.element.querySelector('[data-js-form-remove-many-items-button]');
        this.element.addEventListener('change', this.#itemsIdSelectedToggle.bind(this));

        let homeSelector = this.element.querySelector('[data-controller="HomeSectionComponent"]');
        this.interactive = homeSelector === null || typeof homeSelector.dataset.interactive === 'undefined' ? false : true;

        this.#itemsIdSelectedAddAll();
        this.#buttonRemoveMultiToggle();
    }

    disconnect() {
        this.element.removeEventListener('change', this.#itemsIdSelectedToggle);
    }

    #buttonRemoveMultiToggle() {
        if (this.removeMultiButtonTag === null) {
            return;
        }

        if (this.itemsIdSelected.length === 0) {
            this.removeMultiButtonTag.disabled = true;

            return;
        }

        this.removeMultiButtonTag.disabled = false;
    }

    #itemsIdSelectedToggle(event) {
        if (event.target.tagName !== 'input' && event.target.type !== 'checkbox') return;

        const listItem = event.target.closest('[data-js-list-item]');
        const listItemData = JSON.parse(listItem.dataset.itemData);

        if (!event.target.checked) {
            this.itemsIdSelected = this.itemsIdSelected.filter((item) => item.id !== listItemData.id);
            this.#buttonRemoveMultiToggle();

            return;
        }

        this.itemsIdSelected.push({
            'id': listItemData.id,
            'name': listItemData.name
        });
        this.#buttonRemoveMultiToggle();
    }

    #itemsIdSelectedAddAll() {
        this.listItemsCheckboxes.forEach((checkbox) => {
            const listItem = checkbox.closest('[data-js-list-item]');
            const listItemData = JSON.parse(listItem.dataset.itemData);

            if (checkbox.checked) {
                this.itemsIdSelected.push({
                    'id': listItemData.id,
                    'name': listItemData.name
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
