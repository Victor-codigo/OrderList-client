import { Controller } from '@hotwired/stimulus';
import * as event from 'App/modules/Event';
import * as communication from 'App/modules/ControllerCommunication';

export default class extends Controller {
    /**
     * @type {HTMLElement}
     */
    #itemPriceAddContainer;

    /**
     * @type {HTMLTemplateElement}
     */
    #itemPriceAddTemplate;

    /**
     * @type {HTMLButtonElement}
     */
    #itemPriceAddButton;

    connect() {
        this.#itemPriceAddContainer = this.element.querySelector('[data-js-items-add-container]');
        this.#itemPriceAddTemplate = this.element.querySelector('#item_add_template');
        this.#itemPriceAddButton = this.element.querySelector('[data-js-item-add-button]');

        this.#itemPriceAddButton.addEventListener('click', this.#addItem.bind(this));

        this.#clearItemContainer();

        event.addEventListenerDelegate({
            element: this.#itemPriceAddContainer,
            elementDelegateSelector: '[data-js-item-remove-button]',
            eventName: 'click',
            callbackListener: this.#handlerRemoveItemEvent.bind(this),
            eventOptions: {}
        });

        event.addEventListenerDelegate({
            element: this.#itemPriceAddContainer,
            elementDelegateSelector: '[data-js-item-name]',
            eventName: 'click',
            callbackListener: this.#sendMessageItemPriceCLickedToParent.bind(this),
            eventOptions: {}
        });
    }

    disconnect() {
        this.#itemPriceAddButton.removeEventListener('click', this.#addItem);
        event.removeEventListenerDelegate(this.#itemPriceAddContainer, 'click', this.#sendMessageItemPriceCLickedToParent);
        event.removeEventListenerDelegate(this.#itemPriceAddContainer, 'click', this.#sendMessageItemPriceCLickedToParent);
    }

    /**
     * @param {string|null} [id]
     * @param {string|null} [name]
     * @param {number|null} [price]
     */
    #addItem(id = null, name = null, price = null) {
        /** @type {HTMLElement} */
        const itemPriceAddTemplate = this.#itemPriceAddTemplate.content.cloneNode(true);


        if (id !== null) {
            /** @type {HTMLInputElement} */
            const itemIdTag = itemPriceAddTemplate.querySelector('[data-js-item-id]');
            itemIdTag.value = id;
        }

        if (name !== null) {
            /** @type {HTMLInputElement} */
            const itemNameTag = itemPriceAddTemplate.querySelector('[data-js-item-name]');
            itemNameTag.value = name;
        }

        if (price !== null) {
            /** @type {HTMLInputElement} */
            const itemPriceTag = itemPriceAddTemplate.querySelector('[data-js-item-price]');
            itemPriceTag.value = price.toString();
        }

        this.#itemPriceAddContainer.appendChild(itemPriceAddTemplate);
    }

    /**
     * @typedef ItemPriceAdd
     * @property {string|null} id
     * @property {string} name
     * @property {number} price
     */
    /**
     * @returns {ItemPriceAdd[]}
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

    /**
     * @param {HTMLElement} itemPriceAddContainer
     */
    #removeItem(itemPriceAddContainer) {
        this.#itemPriceAddContainer.removeChild(itemPriceAddContainer);
    }

    #clearItemContainer() {
        this.#itemPriceAddContainer.innerHTML = '';
    }

    /**
     * @param {HTMLInputElement} elementTargetEvent
     * @param {Event} event
     */
    #sendMessageItemPriceCLickedToParent(elementTargetEvent, event) {
        /** @type {HTMLInputElement} */
        const itemPriceAddContainer = elementTargetEvent.closest('[data-js-item-container]');
        const itemsAdded = this.#getItemsAddedValue();
        /** @type {HTMLInputElement} */
        const itemId = itemPriceAddContainer.querySelector('[data-js-item-id]');

        communication.sendMessageToParentController(this.element, 'itemPriceSelected', {
            id: itemId.value,
            name: elementTargetEvent.value,
            itemsAdded: itemsAdded
        });
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     */
    handleMessageClear({ detail: { content } }) {
        this.#clearItemContainer();
    }

    /**
     * @param {HTMLElement} elementTargetEvent
     * @param {Event} event
     */
    #handlerRemoveItemEvent(elementTargetEvent, event) {
        /** @type {HTMLElement} */
        const itemPriceAddContainer = elementTargetEvent.closest('[data-js-item-container]');

        this.#removeItem(itemPriceAddContainer);
    }

    /**
     *
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {Array<{id: string, name: string, price: number}>} event.detail.content.items
     */
    handleMessageAddItems({ detail: { content } }) {
        content.items.forEach((item) => this.#addItem(item.id, item.name, item.price));
    }
}