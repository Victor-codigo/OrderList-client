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
        this.modalBeforeAttributeId = this.element.dataset.modalBeforeAttributeId;
        this.urlPathShopsImages = this.element.dataset.urlPathShopsImages;
        this.urlNoShopsImage = this.element.dataset.urlNoShopsImage
        this.shopImageTitle = this.element.dataset.shopImageTitle
        this.shopsNotSelectable = [];

        this.paginatorContentLoaderJsComponent = this.element.querySelector('[data-controller="PaginatorContentLoaderJsComponent"]');
        this.paginatorJsComponent = this.element.querySelector('[data-controller="PaginatorJsComponent"]');
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
     */
    handleMessageBeforeShowed({ detail: { content } }) {
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
        }
        );
    }

    #sendMessagePageChangeToPaginatorJsComponent(page) {
        communication.sendMessageToChildController(this.paginatorContentLoaderJsComponent, 'changePage', {
            page: page
        },
            'PaginatorJsComponent'
        );
    }

    #sendMessageShopsListShopSelected(shopId, shopName) {
        communication.sendMessageToNotRelatedController(this.element, 'shopSelected', {
            shopId: shopId,
            shopName: shopName
        });
    }

    #sendMessagePagesTotalToPaginatorJsComponent(pagesTotal) {
        communication.sendMessageToChildController(this.paginatorJsComponent, 'pagesTotal', {
            pagesTotal: pagesTotal
        });
    }
}