import { Controller } from '@hotwired/stimulus';
import * as form from 'App/modules/form';
import * as encodedUrlParameter from 'App/modules/EncodedUrlParameter';
import * as communication from 'App/modules/ControllerCommunication';
import * as FormItemPriceAddTrait from 'App/Twig/Components/Controls/ItemPriceAdd/ItemPriceAddFormTrait';
import ModalManager from 'App/modules/ModalManager/ModalManager';
import { MODAL_CHAINS } from 'App/Config';


const SHOP_NAME_PLACEHOLDER = '--shop_name--';

export default class ShopModifyController extends Controller {
    /**
     * @type {ModalManager|null}
     */
    #modalManager = null;
    get modalManager() { return this.#modalManager; }

    /**
     * @type {HTMLElement}
     */
    #itemPriceAddComponentTag;

    /**
     * @type {HTMLInputElement}
     */
    shopNameTag;

    /**
     * @type {HTMLTextAreaElement}
     */
    shopDescriptionTag;
    /**
     * @type {HTMLElement}
     */
    shopAvatarTag;

    constructor(context) {
        Object.assign(ShopModifyController.prototype, FormItemPriceAddTrait);

        super(context);
    }

    /**
     * @this {ShopModifyController & FormItemPriceAddTrait}
     */
    connect() {
        this.#itemPriceAddComponentTag = this.element.querySelector('[data-controller="ItemPriceAddComponent"]');
        this.shopNameTag = this.element.querySelector('[data-js-shop-name]');
        this.shopDescriptionTag = this.element.querySelector('[data-js-shop-description]');
        this.shopAvatarTag = this.element.querySelector('[data-controller="ImageAvatarComponent"]')

        this.formValidate();
        this.setItemPriceAddEvents();
    }

    /**
     * @this {ShopModifyController & FormItemPriceAddTrait}
     */
    disconnect() {
        this.removeItemPriceAddEvents();
    }

    formValidate() {
        form.validate(this.element, null);
    }

    modalClose() {
        this.modalManager.close();
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.imageRemove
     */
    setImageAvatarAsRemoved(event) {
        let imageRemovedField = this.element.querySelector('[data-js-image-remove]');

        if (event.detail.imageRemove) {
            imageRemovedField.value = "true";

            return;
        }

        imageRemovedField.removeAttribute('value');
    }

    /**
     * @param {import('App/Config').ItemData} shopData
     */
    setFormFieldValues(shopData) {
        this.shopNameTag.value = shopData.name;
        this.element.action = this.element.dataset.actionPlaceholder.replace(
            SHOP_NAME_PLACEHOLDER,
            encodedUrlParameter.encodeUrlParameter(shopData.name)
        );
        this.shopDescriptionTag.value = shopData.description;
        this.sendMessageAvatarSetImageEventToImageAvatarComponent(shopData.image);
        this.sendMessageClearToItemsAddComponent();
        this.sendMessageAddItemsToItemsAddComponent(shopData.itemsPrices)
    }

    #setModalData() {
        if (this.modalManager.getChainCurrent() !== null
            && this.modalManager.getChainCurrent().getName() !== MODAL_CHAINS.shopModifyChain.name) {
            return;
        }

        this.#modalManager
            .addModal(MODAL_CHAINS.shopModifyChain.name, MODAL_CHAINS.shopModifyChain.modals.shopModify.modalId)
            .addModal(MODAL_CHAINS.shopModifyChain.name, MODAL_CHAINS.shopModifyChain.modals.productList.modalId)
            .addModal(MODAL_CHAINS.shopModifyChain.name, MODAL_CHAINS.shopModifyChain.modals.productCreate.modalId)
        this.#modalManager.setModalCurrent(MODAL_CHAINS.shopModifyChain.name, MODAL_CHAINS.shopModifyChain.modals.shopModify.modalId);
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {boolean} event.detail.content.showedFirstTime
     * @param {HTMLElement} event.detail.content.triggerElement
     * @param {ModalManager} event.detail.content.modalManager
     *
     * @this {ShopModifyController & FormItemPriceAddTrait}
     */
    handleMessageBeforeShowed({ detail: { content } }) {
        const modalBefore = content.modalManager.getModalOpenedBefore();
        this.#modalManager = content.modalManager;

        if (modalBefore === null) {
            this.#setModalData();

            return;
        }

        const modalBeforeSharedData = this.getModalBeforeSharedData();

        if (modalBeforeSharedData !== null) {
            this.setItemCurrentData(modalBeforeSharedData.id, modalBeforeSharedData.name);
        }
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {string} event.detail.content.id
     * @param {string} event.detail.content.name
     * @param {Array} event.detail.content.itemsAdded
     */
    handleItemNameClickEvent({ detail: { content } }) {
        const chainCurrentName = this.modalManager.getChainCurrent().getName();

        this.modalManager.openNewModal(MODAL_CHAINS[chainCurrentName].modals.shopModify.open.productsListModal, {
            itemsNotSelectable: content.itemsAdded
        });
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {object} event.detail.content.itemData
     */
    handleMessageHomeListItemModify({ detail: { content } }) {
        this.setFormFieldValues(content.itemData);
    }

    /**
     * @param {string} shopImageUrl
     */
    sendMessageAvatarSetImageEventToImageAvatarComponent(shopImageUrl) {
        communication.sendMessageToNotRelatedController(this.element, 'avatarSetImage', {
            imageUrl: shopImageUrl
        },
            'ImageAvatarComponent'
        );
    }

    /**
     * @param {Array<{id: string, name: string, price: number}>} items
     */
    sendMessageAddItemsToItemsAddComponent(items) {
        communication.sendMessageToChildController(this.#itemPriceAddComponentTag, 'addItems', {
            items: items
        });
    }

    sendMessageClearToItemsAddComponent() {
        communication.sendMessageToChildController(this.#itemPriceAddComponentTag, 'clear');
    }
}
