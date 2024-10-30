import { Controller } from '@hotwired/stimulus';
import * as communication from 'App/modules/ControllerCommunication';

export default class extends Controller {

    /**
     * @type {boolean}
     */
    interactive;

    connect() {
        this.checkbox = this.element.querySelector('[data-js-checkbox]');

        let homeSelection = this.element.closest('[data-controller="HomeSectionComponent"]');
        this.interactive = homeSelection === null || typeof homeSelection.dataset.interactive === 'undefined' ? false : true;

        if (this.checkbox !== null) {
            this.checkbox.addEventListener('change', this.#sendMessageHomeListItemSelectedToParent.bind(this));
        }
    }

    disconnect() {
        this.checkbox.removeEventListener('change', this.#sendMessageHomeListItemSelectedToParent);
    }

    /**
     * @returns {object}
     */
    getItemData() {
        return JSON.parse(this.element.dataset.itemData);
    }

    /**
     * @param {object} itemData
     */
    setItemData(itemData) {
        this.element.dataset.itemData = JSON.stringify(itemData);
    }

    sendMessageHomeListItemRemoveEvent() {
        communication.sendMessageToNotRelatedController(this.element, 'removeHomeListItem', {
            items: [this.getItemData()]
        },
            'ItemRemoveComponent'
        );
    }

    sendMessageHomeListItemModifyToParent() {
        communication.sendMessageToParentController(this.element, 'homeListItemModify', {
            itemData: JSON.parse(this.element.dataset.itemData)
        });
    }

    #sendMessageHomeListItemSelectedToParent() {
        communication.sendMessageToParentController(this.element, 'onHomeListItemSelectedEvent', {
            id: this.element.dataset.itemData.id
        });
    }

    sendMessageHomeListItemInfoToParent() {
        communication.sendMessageToParentController(this.element, 'homeListItemInfo', {
            itemData: JSON.parse(this.element.dataset.itemData)
        },
            'HomeListItemComponent'
        );
    }
}
