import { Controller } from '@hotwired/stimulus';
import * as form from 'App/modules/form';
import * as encodedUrlParameter from 'App/modules/EncodedUrlParameter';
import * as communication from 'App/modules/ControllerCommunication';
import ModalManager from 'App/modules/ModalManager/ModalManager';
import * as FormItemPriceAddTrait from 'App/Twig/Components/Controls/ItemPriceAdd/ItemPriceAddFormTrait';
import { MODAL_CHAINS } from 'App/Config';

const PRODUCT_NAME_PLACEHOLDER = '--product_name--';

export default class ProductModifyController extends Controller {
    /**
     * @type {ModalManager|null}
     */
    #modalManager = null;
    get modalManager() { return this.#modalManager; }

    /**
     * @type {HTMLInputElement|null}
     */
    #productNameTag;

    /**
     * @type {HTMLTextAreaElement|null}
     */
    #productDescriptionTag;

    /**
     * @type {HTMLElement}
     */
    #itemAddComponentTag;

    constructor(context) {
        Object.assign(ProductModifyController.prototype, FormItemPriceAddTrait);

        super(context);
    }

    /**
     * @this {ProductModifyController & FormItemPriceAddTrait}
     */
    connect() {
        this.#productNameTag = this.element.querySelector('[data-js-product-name]');
        this.#productDescriptionTag = this.element.querySelector('[data-js-product-description]');
        this.#itemAddComponentTag = this.element.querySelector('[data-controller="ItemPriceAddComponent"]');

        this.formValidate();
        this.setItemPriceAddEvents();
    }

    /**
     * @this {ProductModifyController & FormItemPriceAddTrait}
     */
    disconnect() {
        this.removeItemPriceAddEvents();
    }

    formValidate() {
        form.validate(this.element, null);
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
     * @param {import('App/Config').ItemData} productData
     */
    setFormFieldValues(productData) {
        this.#productNameTag.value = productData.name;
        this.element.action = this.element.dataset.actionPlaceholder.replace(
            PRODUCT_NAME_PLACEHOLDER,
            encodedUrlParameter.encodeUrlParameter(productData.name)
        );
        this.#productDescriptionTag.value = productData.description;
        this.sendMessageAvatarSetImageEventToImageAvatarComponent(productData.image);
        this.sendMessageClearToItemsAddComponent();
        this.sendMessageAddItemsToItemsAddComponent(productData.shops)
    }

    #setModalData() {
        if (!this.#modalManager.modalChainExists(MODAL_CHAINS.productModifyChain.name)) {
            this.#modalManager
                .addModal(MODAL_CHAINS.productModifyChain.name, MODAL_CHAINS.productModifyChain.modals.productModify)
                .addModal(MODAL_CHAINS.productModifyChain.name, MODAL_CHAINS.productModifyChain.modals.shopList)
                .addModal(MODAL_CHAINS.productModifyChain.name, MODAL_CHAINS.productModifyChain.modals.shopCreate)
        }

        this.#modalManager.setModalCurrent(MODAL_CHAINS.productModifyChain.name, MODAL_CHAINS.productModifyChain.modals.productModify);
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {boolean} event.detail.content.showedFirstTime
     * @param {HTMLElement} event.detail.content.triggerElement
     * @param {ModalManager} event.detail.content.modalManager
     *
     * @this {ProductModifyController & FormItemPriceAddTrait}
     */
    handleMessageBeforeShowed({ detail: { content } }) {
        const modalBefore = content.modalManager.getModalOpenedBefore();
        this.#modalManager = content.modalManager;
        this.#setModalData();

        if (modalBefore === null) {
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
     * @param {object} event.detail.content.itemData
     */
    handleMessageHomeListItemModify({ detail: { content } }) {
        this.setFormFieldValues(content.itemData);
    }

    /**
     * @param {string} productImageUrl
     */
    sendMessageAvatarSetImageEventToImageAvatarComponent(productImageUrl) {
        communication.sendMessageToNotRelatedController(this.element, 'avatarSetImage', {
            imageUrl: productImageUrl
        },
            'ImageAvatarComponent'
        );
    }

    /**
     * @param {Array<{id: string, name: string, price: number}>} items
     */
    sendMessageAddItemsToItemsAddComponent(items) {
        communication.sendMessageToChildController(this.#itemAddComponentTag, 'addItems', {
            items: items
        });
    }

    sendMessageClearToItemsAddComponent() {
        communication.sendMessageToChildController(this.#itemAddComponentTag, 'clear');
    }
}
