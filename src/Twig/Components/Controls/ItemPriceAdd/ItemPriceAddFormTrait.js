import * as event from 'App/modules/Event';
import { MODAL_CHAINS } from 'App/Config';

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

/**
 * @param {string} shopId
 * @param {string} shopName
 */
export function setShopCurrentData(shopId, shopName) {
    /** @type {HTMLInputElement} */
    const itemPriceId = this.itemPriceGroupCurrent.querySelector('[data-js-item-id]');
    /** @type {HTMLInputElement} */
    const itemPriceName = this.itemPriceGroupCurrent.querySelector('[data-js-item-name]');

    itemPriceId.value = shopId;
    itemPriceName.value = shopName;
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

    if (typeof modalBeforeSharedData.shopId === 'undefined') {
        return null;
    }

    return { ...returnDefault, ...modalBeforeSharedData.shopId };
}
