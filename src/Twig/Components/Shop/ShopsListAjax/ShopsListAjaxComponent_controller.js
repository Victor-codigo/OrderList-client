import { Controller } from '@hotwired/stimulus';
import ListItems from '/assets/modules/ListItems';
import * as event from '/assets/modules/Event';
import * as communication from '/assets/modules/ControllerCommunication';

/**
 * @event ShopsListAjaxComponent:shopSelected
 * @event PaginatorContentLoaderJsComponent:initialize
 */
export default class extends Controller {
    connect() {
        this.responseManageCallback = this.#responseManageCallback.bind(this);
        this.postResponseManageCallback = this.#postResponseManageCallback.bind(this);
        this.modalBeforeAttributeId = this.element.dataset.modalBeforeAttributeId;
        this.urlPathShopsImages = this.element.dataset.urlPathShopsImages;
        this.urlNoShopsImage = this.element.dataset.urlNoShopsImage
        this.shopImageTitle = this.element.dataset.shopImageTitle

        this.paginatorContentLoaderJsComponent = this.element.querySelector('[data-controller="PaginatorContentLoaderJsComponent"]');
        this.paginatorJsComponent = this.element.querySelector('[data-controller="PaginatorJsComponent"]');
    }

    #responseManageCallback(responseData) {
        const itemsData = responseData['shops'].map((shopData) => {
            return {
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
                        "data-bs-toggle": "modal"
                    }
                }
            }
        });

        this.#sendMessagePagesTotalToPaginatorJsComponent(responseData.pages_total);

        return new ListItems({}, itemsData);
    }

    #postResponseManageCallback(container) {
        const list = container.querySelector('ul');

        event.addEventListenerDelegate({
            element: list,
            elementDelegateSelector: 'li',
            eventName: 'click',
            callbackListener: this.handleMessageShopSelected.bind(this)
        });
    }

    handleMessageShopSelected(itemTag, event) {
        const itemData = JSON.parse(itemTag.dataset.data);

        this.#sendMessageShopsListShopSelected(itemData.id, itemData.name);
    }

    handleMessageConnected() {
        this.#sendMessageInitializeToPaginatorContentLoaderJsComponent();
    }

    handleMessageBeforeShowed({ detail: { content } }) {
        if (!content.showedFirstTime) {
            return;
        }

        this.#sendMessagePageChangeToPaginatorJsComponent(1);
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
            'PaginatorJsComponent');
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