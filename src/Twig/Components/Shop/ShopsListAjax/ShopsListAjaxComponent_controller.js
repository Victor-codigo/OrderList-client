import { Controller } from '@hotwired/stimulus';
import ListItems from 'App/modules/ListItems';
import * as event from 'App/modules/Event';
import * as communication from 'App/modules/ControllerCommunication';

/**
 * @event ShopsListAjaxComponent:shopSelected
 * @event PaginatorContentLoaderJsComponent:initialize
 */
export default class extends Controller {
    connect() {
        this.modalBeforeAttributeId = null;
        this.modalBeforeControllerEventHandler = null;
        this.urlPathShopsImages = this.element.dataset.urlPathShopsImages;
        this.urlNoShopsImage = this.element.dataset.urlNoShopsImage
        this.shopImageTitle = this.element.dataset.shopImageTitle
        this.shopsNotSelectable = [];

        this.paginatorContentLoaderJsComponent = this.element.querySelector('[data-controller="PaginatorContentLoaderJsComponent"]');
        this.paginatorJsComponent = this.element.querySelector('[data-controller="PaginatorJsComponent"]');
        this.backButtonTag = this.element.querySelector('[data-js-back-button]');
    }

    /**
     * @param {*} responseData
     * @returns {ListItems}
     */
    #responseManageCallback(responseData) {
        const itemsData = responseData['shops']
            .map((shopData) => {
                const itemData = {
                    id: shopData.id,
                    name: shopData.name,
                    image: {
                        title: this.shopImageTitle.replace('{shop_name}', shopData.name),
                        alt: this.shopImageTitle.replace('{shop_name}', shopData.name),
                        src: shopData.image === null
                            ? this.urlNoShopsImage
                            : `${this.urlPathShopsImages}/${shopData.image}`
                    },
                    data: {
                        id: shopData.id,
                        name: shopData.name,
                    },
                    item: {
                        htmlAttributes: {
                            "data-bs-target": '#' + this.modalBeforeAttributeId,
                            "data-bs-toggle": "modal",
                        },
                        cssClasses: []
                    },
                };

                if (this.shopsNotSelectable.includes(shopData.id)) {
                    itemData.item.cssClasses.push("disabled");
                }

                return itemData;
            });

        this.#sendMessagePagesTotalToPaginatorJsComponent(responseData.pages_total);

        return new ListItems({}, itemsData);
    }

    /**
     * @param {HTMLElement} container
     * @param {Event} eventData
     */
    #postResponseManageCallback(container, eventData) {
        const list = container.querySelector('ul');

        event.addEventListenerDelegate({
            element: list,
            elementDelegateSelector: 'li',
            eventName: 'click',
            callbackListener: this.handleMessageShopSelected.bind(this),
            eventOptions: {}
        });
    }

    /**
     * @param {string} modalBeforeAttributeId
     */
    #setModalBefore(modalBeforeAttributeId) {
        this.modalBeforeAttributeId = modalBeforeAttributeId;
        this.backButtonTag.dataset.bsTarget = '#' + modalBeforeAttributeId;
    }

    /**
     * @param {HTMLElement} itemTag
     * @param {Event} event
     */
    handleMessageShopSelected(itemTag, event) {
        const itemData = JSON.parse(itemTag.dataset.data);

        this.#sendMessageShopsListShopSelected(itemData.id, itemData.name);
    }

    handleMessageConnected() {
        this.#sendMessageInitializeToPaginatorContentLoaderJsComponent();
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {boolean} event.detail.content.showedFirstTime
     * @param {HTMLElement} event.detail.content.triggerElement
     * @param {object} event.detail.content.triggerElementData
     * @param {string} event.detail.content.triggerElementData.modalBefore
     * @param {string} event.detail.content.triggerElementData.controllerModalEventHandler
     */
    handleMessageBeforeShowed({ detail: { content } }) {
        if (content.triggerElementData.modalBefore !== null) {
            this.#setModalBefore(content.triggerElementData.modalBefore);
        }

        this.modalBeforeControllerEventHandler = content.triggerElementData.controllerModalEventHandler;
        this.#sendMessagePageChangeToPaginatorJsComponent(1);
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {object[]} event.detail.content.shopsAdded
     * @param {string} event.detail.content.shopsAdded.id
     * @param {string} event.detail.content.shopsAdded.name
     * @param {string} event.detail.content.shopsAdded.price
     */
    handleMessageItemPriceSelected({ detail: { content } }) {
        this.shopsNotSelectable = content.shopsAdded.map((shopData) => shopData.id);
    }

    #sendMessageInitializeToPaginatorContentLoaderJsComponent() {
        communication.sendMessageToChildController(this.paginatorContentLoaderJsComponent, 'initialize', {
            responseManageCallback: this.#responseManageCallback.bind(this),
            postResponseManageCallback: this.#postResponseManageCallback.bind(this)
        });
    }

    /**
     * @param {number} page
     */
    #sendMessagePageChangeToPaginatorJsComponent(page) {
        communication.sendMessageToChildController(this.paginatorContentLoaderJsComponent, 'changePage', {
            page: page
        },
            'PaginatorJsComponent'
        );
    }

    /**
     * @param {string} shopId
     * @param {string} shopName
     */
    #sendMessageShopsListShopSelected(shopId, shopName) {
        communication.sendMessageToNotRelatedController(this.element, 'shopSelected', {
            shopId: shopId,
            shopName: shopName,
        },
            `[${this.element.dataset.controller}|>${this.modalBeforeControllerEventHandler}]`
        );
    }

    /**
     * @param {number} pagesTotal
     */
    #sendMessagePagesTotalToPaginatorJsComponent(pagesTotal) {
        communication.sendMessageToChildController(this.paginatorJsComponent, 'pagesTotal', {
            pagesTotal: pagesTotal
        });
    }
}