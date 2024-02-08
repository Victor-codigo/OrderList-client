import { Controller } from '@hotwired/stimulus';
import ListItems from 'App/modules/ListItems';
import * as event from 'App/modules/Event';
import * as communication from 'App/modules/ControllerCommunication';
import ModalManager from 'App/modules/ModalManager/ModalManager';
import { MODAL_CHAINS } from 'App/Config';

const LIST_ATTRIBUTE_SELECTOR = "data-js-list-shops";
const LIST_ITEM_ATTRIBUTE_SELECTOR = "data-js-list-shops-item";

/**
 * @event PaginatorContentLoaderJsComponent:initialize
 */
export default class extends Controller {
    /**
     * @type {ModalManager}
     */
    #modalManager;

    /**
     * @type {string}
     */
    #urlPathShopsImages;

    /**
     * @type {string}
     */
    #urlNoShopsImage;

    /**
     * @type {string}
     */
    #shopImageTitle;

    /**
     * @type {string[]}
     */
    #shopsNotSelectable;

    /**
     * @type {HTMLElement}
     */
    #paginatorContentLoaderJsComponent;

    /**
     * @type {HTMLElement}
     */
    #paginatorJsComponent;

    /**
     * @type {HTMLButtonElement}
     */
    #backButtonTag;

    /**
     * @type {HTMLButtonElement}
     */
    #createShopButtonTag;

    connect() {
        this.#urlPathShopsImages = this.element.dataset.urlPathShopsImages;
        this.#urlNoShopsImage = this.element.dataset.urlNoShopsImage
        this.#shopImageTitle = this.element.dataset.shopImageTitle
        this.#shopsNotSelectable = [];

        this.#paginatorContentLoaderJsComponent = this.element.querySelector('[data-controller="PaginatorContentLoaderJsComponent"]');
        this.#paginatorJsComponent = this.element.querySelector('[data-controller="PaginatorJsComponent"]');
        this.#backButtonTag = this.element.querySelector('[data-js-back-button]');
        this.#createShopButtonTag = this.element.querySelector('[data-js-create-shop-button]');

        this.#backButtonTag.addEventListener('click', this.#openModalBefore.bind(this));
        this.#createShopButtonTag.addEventListener('click', this.#openModalCreateShop.bind(this))
        event.addEventListenerDelegate({
            element: this.element,
            elementDelegateSelector: '[data-js-list-shops-item]',
            eventName: 'click',
            callbackListener: this.#openModalShopSelected.bind(this),
            eventOptions: {}
        });
    }

    disconnect() {
        this.#backButtonTag.removeEventListener('click', this.#openModalBefore);
        this.#createShopButtonTag.removeEventListener('click', this.#openModalCreateShop);
        event.removeEventListenerDelegate(this.element, 'click', this.#openModalShopSelected);
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
                        title: this.#shopImageTitle.replace('{shop_name}', shopData.name),
                        alt: this.#shopImageTitle.replace('{shop_name}', shopData.name),
                        src: shopData.image === null
                            ? this.#urlNoShopsImage
                            : `${this.#urlPathShopsImages}/${shopData.image}`
                    },
                    data: {
                        id: shopData.id,
                        name: shopData.name,
                    },
                    item: {
                        htmlAttributes: {
                            [LIST_ITEM_ATTRIBUTE_SELECTOR]: ""
                        },
                        cssClasses: []
                    },
                };

                if (this.#shopsNotSelectable.includes(shopData.id)) {
                    itemData.item.cssClasses.push("disabled");
                }

                return itemData;
            });

        this.#sendMessagePagesTotalToPaginatorJsComponent(responseData.pages_total);

        const listData = {
            htmlAttributes: {
                [LIST_ATTRIBUTE_SELECTOR]: ""
            }
        };

        return new ListItems(listData, itemsData);
    }

    /**
     * @param {Event} event
     */
    #openModalBefore(event) {
        this.#modalManager.openModalBefore();
    }

    /**
     * @param {HTMLElement} relatedTarget
     * @param {Event} event
     */
    #openModalShopSelected(relatedTarget, event) {
        const itemData = JSON.parse(relatedTarget.dataset.data);
        this.#modalManager.openNewModal(this.#modalManager.getModalBeforeInChain().getModalId(), {
            shopId: {
                id: itemData.id,
                name: itemData.name
            }
        });
    }

    #openModalCreateShop() {
        const chainCurrentName = this.#modalManager.getChainCurrent().getName();

        this.#modalManager.openNewModal(MODAL_CHAINS[chainCurrentName].shopCreate);
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
     * @param {ModalManager} event.detail.content.modalManager
     */
    handleMessageBeforeShowed({ detail: { content } }) {
        this.#modalManager = content.modalManager;
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
        communication.sendMessageToChildController(this.#paginatorContentLoaderJsComponent, 'initialize', {
            responseManageCallback: this.#responseManageCallback.bind(this),
            postResponseManageCallback: () => { }
        });
    }

    /**
     * @param {number} page
     */
    #sendMessagePageChangeToPaginatorJsComponent(page) {
        communication.sendMessageToChildController(this.#paginatorContentLoaderJsComponent, 'changePage', {
            page: page
        },
            'PaginatorJsComponent'
        );
    }

    /**
     * @param {number} pagesTotal
     */
    #sendMessagePagesTotalToPaginatorJsComponent(pagesTotal) {
        communication.sendMessageToChildController(this.#paginatorJsComponent, 'pagesTotal', {
            pagesTotal: pagesTotal
        });
    }
}