import { Controller } from '@hotwired/stimulus';
import * as event from '/assets/modules/Event';
import * as communication from '/assets/modules/ControllerCommunication';

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

        event.addEventListenerDelegate({
            element: this.itemPriceAddContainer,
            elementDelegateSelector: '[data-js-item-name]',
            eventName: 'click',
            callbackListener: this.#sendMessageItemPriceCLickedToParent.bind(this)
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

    /**
     * @returns {{
     *  id: string,
     *  name: string,
     *  price: float
     * }}
     */
    #getItemsAddedValue() {
        const itemGroupsAddedTag = this.element.querySelectorAll('[data-js-item-group]');

        return Array.from(itemGroupsAddedTag)
            .map((itemGroupTag) => {
                const id = itemGroupTag.querySelector('[data-js-item-id]').value;
                const name = itemGroupTag.querySelector('[data-js-item-name]').value;
                const price = parseFloat(itemGroupTag.querySelector('[data-js-item-price]').value);

                return {
                    id: id === "" ? null : id,
                    name: name,
                    price: price
                };
            })
            .filter((itemGroupTag) => itemGroupTag.id !== null);
    }

    #handlerRemoveItemEvent(elementTargetEvent, event) {
        const itemPriceAddContainer = elementTargetEvent.closest('[data-js-item-container]');

        this.#removeItem(itemPriceAddContainer);
    }

    handleMessageclear({ detail: { content } }) {
        this.#clearItemContainer();
    }

    #removeItem(itemPriceAddContainer) {
        this.itemPriceAddContainer.removeChild(itemPriceAddContainer);
    }

    #clearItemContainer() {
        this.itemPriceAddContainer.innerHTML = '';
    }

    #sendMessageItemPriceCLickedToParent(elementTargetEvent, event) {
        const itemPriceAddContainer = elementTargetEvent.closest('[data-js-item-container]');
        const itemsAdded = this.#getItemsAddedValue();

        communication.sendMessageToParentController(this.element, 'itemPriceSelected', {
            id: itemPriceAddContainer.querySelector('[data-js-item-id]').value,
            name: elementTargetEvent.value,
            itemsAdded: itemsAdded
        });
    }
}