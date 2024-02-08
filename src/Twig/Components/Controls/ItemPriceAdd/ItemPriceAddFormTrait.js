import * as event from 'App/modules/Event';
import { MODAL_CHAINS } from 'App/Config';

/**
 * @type {HTMLElement|null}
 */
let itemPriceGroupCurrent = null;

export function setItemPriceAddEvents() {
    event.addEventListenerDelegate({
        element: this.element,
        elementDelegateSelector: '[data-js-item-group]',
        eventName: 'click',
        callbackListener: this.setPriceGroupCurrent.bind(this),
        eventOptions: {}
    });

    event.addEventListenerDelegate({
        element: this.element,
        elementDelegateSelector: '[data-js-item-name]',
        eventName: 'click',
        callbackListener: this.handleItemNameClickEvent.bind(this),
        eventOptions: {}
    });
}

export function removeItemPriceAddEvents() {
    event.removeEventListenerDelegate(this.element, 'click', this.setPriceGroupCurrent);
    event.removeEventListenerDelegate(this.element, 'click', this.handleItemNameClickEvent);
}

/**
 * @param {HTMLElement} itemGroupTag
 * @param {Event} event
 */
export function setPriceGroupCurrent(itemGroupTag, event) {
    itemPriceGroupCurrent = itemGroupTag;
}

/**
 * @param {string} itemId
 * @param {string} itemName
 */
export function setItemCurrentData(itemId, itemName) {
    /** @type {HTMLInputElement} */
    const itemPriceId = itemPriceGroupCurrent.querySelector('[data-js-item-id]');
    /** @type {HTMLInputElement} */
    const itemPriceName = itemPriceGroupCurrent.querySelector('[data-js-item-name]');

    itemPriceId.value = itemId;
    itemPriceName.value = itemName;
}

/**
 * @returns {{id: string|null, name: string|null}}
 */
export function getModalBeforeSharedData() {
    const modalBeforeSharedData = this.modalManager.getModalOpenedBeforeData();
    const returnDefault = {
        id: null,
        name: null
    };

    if (typeof modalBeforeSharedData.itemData === 'undefined') {
        return null;
    }

    return { ...returnDefault, ...modalBeforeSharedData.itemData };
}

/**
 * @param {HTMLElement} itemNameTag
 * @param {object} event
 * @param {object} event.detail
 * @param {object} event.detail.content
 * @param {object} event.detail.content.chainCurrentName
 */
export function handleItemNameClickEvent(itemNameTag, event) {
    const chainCurrentName = this.modalManager.getChainCurrent().getName();

    this.modalManager.openNewModal(MODAL_CHAINS[chainCurrentName].modals.shopList);
}
