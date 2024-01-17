import { Controller } from '@hotwired/stimulus';
import * as event from '../../../../../assets/modules/Event';

export default class extends Controller {
    connect() {
        this.itemPriceAddContainer = this.element.querySelector('[data-js-items-add-container]');
        this.itemPriceAddTemplate = this.element.querySelector('#item_add_template');
        this.itemPriceAddButton = this.element.querySelector('[data-js-item-add-button]');

        this.itemPriceAddButton.addEventListener('click', this.#addItem.bind(this));

        this.#clearItemContainer();

        event.addEventListenerDelegate({
            element: this.itemPriceAddContainer,
            elementDelegateSelector: '[data-js-item-remove-button]',
            eventName: 'click',
            callbackListener: this.#handlerRemoveItemEvent.bind(this)
        });
    }

    disconnect() {
        this.itemPriceAddButton.removeEventListener('click', this.#addItem);
        event.removeEventListenerDelegate(this.itemPriceAddContainer, 'click');
    }

    #addItem() {
        const itemPriceAddTemplate = this.itemPriceAddTemplate.content.cloneNode(true);

        this.itemPriceAddContainer.appendChild(itemPriceAddTemplate);
    }

    #handlerRemoveItemEvent(elementTargetEvent, event) {
        const itemPriceAddContainer = elementTargetEvent.closest('[data-js-item-container]');

        this.#removeItem(itemPriceAddContainer);
    }

    #removeItem(itemPriceAddContainer) {
        this.itemPriceAddContainer.removeChild(itemPriceAddContainer);
    }

    #clearItemContainer() {
        this.itemPriceAddContainer.innerHTML = '';
    }
}