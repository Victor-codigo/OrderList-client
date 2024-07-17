import { Controller } from '@hotwired/stimulus';
import ListItems from 'App/modules/ListItems';
import * as event from 'App/modules/Event';
import * as communication from 'App/modules/ControllerCommunication';
import ModalManager from 'App/modules/ModalManager/ModalManager';

const LIST_ATTRIBUTE_SELECTOR = "data-js-list-items";
const LIST_ITEM_ATTRIBUTE_SELECTOR = "data-js-list-items-item";

/**
 * @event PaginatorContentLoaderJsComponent:initialize
 */
export default class ItemsListAjaxController extends Controller {
    get listAttributeSelector() { return LIST_ATTRIBUTE_SELECTOR; }
    get listItemAttributeSelector() { return LIST_ITEM_ATTRIBUTE_SELECTOR; }

    /**
     * @type {ModalManager}
     */
    #modalManager;
    get modalManager() { return this.#modalManager; }

    /**
     * @type {string}
     */
    #urlPathItemsImages;

    /**
     * @type {string}
     */
    #urlNoItemsImage;

    /**
     * @type {string}
     */
    #itemImageTitle;

    /**
     * @type {string[]}
     */
    #itemsNotSelectable = [];

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
    #createItemButtonTag;

    connect() {
        this.#urlPathItemsImages = this.element.dataset.urlPathItemsImages;
        this.#urlNoItemsImage = this.element.dataset.urlNoItemsImage
        this.#itemImageTitle = this.element.dataset.itemImageTitle

        this.#paginatorContentLoaderJsComponent = this.element.querySelector('[data-controller="PaginatorContentLoaderJsComponent"]');
        this.#paginatorJsComponent = this.element.querySelector('[data-controller="PaginatorJsComponent"]');
        this.#backButtonTag = this.element.querySelector('[data-js-back-button]');
        this.#createItemButtonTag = this.element.querySelector('[data-js-create-item-button]');

        this.#backButtonTag.addEventListener('click', this.#openModalBefore.bind(this));
        event.addEventListenerDelegate({
            element: this.element,
            elementDelegateSelector: '[data-js-list-items-item]',
            eventName: 'click',
            callbackListener: this.#openModalItemSelected.bind(this),
            eventOptions: {}
        });

        if (this.#createItemButtonTag !== null) {
            this.#createItemButtonTag.addEventListener('click', this.openModalCreateItem.bind(this))
        }
    }

    disconnect() {
        this.#backButtonTag.removeEventListener('click', this.#openModalBefore);
        this.#createItemButtonTag.removeEventListener('click', this.openModalCreateItem);
        event.removeEventListenerDelegate(this.element, 'click', this.#openModalItemSelected);
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
    #openModalItemSelected(relatedTarget, event) {
        const itemData = JSON.parse(relatedTarget.dataset.data);
        this.#modalManager.openNewModal(this.#modalManager.getModalBeforeInChain().getModalId(), {
            itemData: {
                id: itemData.id,
                name: itemData.name
            }
        });
    }

    openModalCreateItem() {
        throw new Error('method [openModalCreateItem], should be overridden');
    }

    #setItemsNotSelectable() {
        const modalBefore = this.#modalManager.getModalOpenedBefore();

        if (modalBefore === null) {
            return;
        }

        const modalBeforeData = this.#modalManager.getModalOpenedBeforeData();

        if (typeof modalBeforeData.itemsNotSelectable === 'undefined') {
            return;
        }

        this.#itemsNotSelectable = modalBeforeData.itemsNotSelectable.map((item) => item.id);
    }

    /**
     * @param {object} responseData
     * @param {number} responseData.page
     * @param {number} responseData.pages_total
     * @param {array} responseData.items
     * @returns {ListItems}
     */
    responseManageCallback(responseData) {
        const itemsData = responseData.items
            .map((itemResponseData) => {
                const itemData = {
                    id: itemResponseData.id,
                    name: itemResponseData.name,
                    image: {
                        title: this.#itemImageTitle.replace('{item_name}', itemResponseData.name),
                        alt: this.#itemImageTitle.replace('{item_name}', itemResponseData.name),
                        src: itemResponseData.image === null || typeof itemResponseData.image === 'undefined'
                            ? this.#urlNoItemsImage
                            : itemResponseData.image,
                        noImage: itemResponseData.image === null || typeof itemResponseData.image === 'undefined' ? true : false
                    },
                    data: {
                        id: itemResponseData.id,
                        name: itemResponseData.name,
                    },
                    item: {
                        htmlAttributes: {
                            [LIST_ITEM_ATTRIBUTE_SELECTOR]: ""
                        },
                        cssClasses: ['item__img--svg']
                    },
                };

                if (this.#itemsNotSelectable.includes(itemResponseData.id)) {
                    itemData.item.cssClasses.push("disabled");
                }

                return itemData;
            });

        this.sendMessagePagesTotalToPaginatorJsComponent(responseData.pages_total);

        const listData = {
            htmlAttributes: {
                [LIST_ATTRIBUTE_SELECTOR]: ""
            },
            cssClasses: []
        };

        return new ListItems(listData, itemsData, {
            text: this.element.dataset.listEmptyText,
            image: this.element.dataset.listEmptyImage,
        });
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
        this.#setItemsNotSelectable();
        this.#sendMessagePageChangeToPaginatorJsComponent(1);
        this.#sendMessageLoadLettersAlphanumericFilter();
    }

    #sendMessageInitializeToPaginatorContentLoaderJsComponent() {
        communication.sendMessageToChildController(this.#paginatorContentLoaderJsComponent, 'initialize', {
            responseManageCallback: (responseData) => this.responseManageCallback(responseData).listTag,
            postResponseManageCallback: () => { },
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

    #sendMessageLoadLettersAlphanumericFilter() {
        communication.sendMessageToChildController(this.#paginatorContentLoaderJsComponent, 'loadLetters', {
            groupId: this.element.dataset.groupId,
            section: this.element.dataset.section
        },
            'PaginatorJsComponent'
        );
    }

    /**
     * @param {number} pagesTotal
     */
    sendMessagePagesTotalToPaginatorJsComponent(pagesTotal) {
        communication.sendMessageToChildController(this.#paginatorJsComponent, 'pagesTotal', {
            pagesTotal: pagesTotal
        });
    }
}