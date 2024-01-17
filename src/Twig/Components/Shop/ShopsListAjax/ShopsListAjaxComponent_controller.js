import { Controller } from '@hotwired/stimulus';
import ListItems from '../../../../../assets/modules/ListItems';
import * as event from '../../../../../assets/modules/Event';

/**
 * @event ShopsListAjaxComponent:onShopSelected
 * @event PaginatorContentLoaderJsComponentEventHandler:onInitialize
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

        this.#triggerPaginatorJsPagesTotal(responseData.pages_total);

        return new ListItems({}, itemsData);
    }

    #postResponseManageCallback(container) {
        const list = container.querySelector('ul');

        event.addEventListenerDelegate({
            element: list,
            elementDelegateSelector: 'li',
            eventName: 'click',
            callbackListener: this.handlerShopListAjaxComponentItemSelected.bind(this)
        });
    }

    handlerShopListAjaxComponentItemSelected(itemTag, event) {
        const itemData = JSON.parse(itemTag.dataset.data);

        this.#triggerShopsListShopSelected(itemData.id, itemData.name);
    }

    handlerPaginatorContentLoaderJsConnected() {
        this.#triggerSHopsListPaginatorContentLoaderInitialize();
    }

    handlerModalBeforeShowed({ detail: { content } }) {
        if (!content.showedFirstTime) {
            return;
        }

        this.#triggerPaginatorPaginatorPageChange(1);
    }

    #triggerSHopsListPaginatorContentLoaderInitialize() {
        event.dispatch(this.paginatorContentLoaderJsComponent, "PaginatorContentLoaderJsComponentEventHandler", "onInitialize", {
            detail: {
                content: {
                    responseManageCallback: this.#responseManageCallback.bind(this),
                    postResponseManageCallback: this.#postResponseManageCallback.bind(this)
                }
            }
        });
    }

    #triggerPaginatorPaginatorPageChange(page) {
        event.dispatch(this.paginatorContentLoaderJsComponent, "PaginatorJsComponent", "onPageChange", {
            detail: {
                content: {
                    page: page
                }
            }
        });
    }

    #triggerShopsListShopSelected(shopId, shopName) {
        event.dispatch(window, "ShopsListAjaxComponent", "onShopSelected", {
            detail: {
                content: {
                    shopId: shopId,
                    shopName: shopName
                }
            }
        });
    }

    #triggerPaginatorJsPagesTotal(pagesTotal) {
        this.paginatorJsComponent.dispatchEvent(new CustomEvent('PaginatorJsComponentEventHandler:pagesTotal', {
            detail: {
                content: {
                    pagesTotal: pagesTotal
                }
            }
        }));
    }
}