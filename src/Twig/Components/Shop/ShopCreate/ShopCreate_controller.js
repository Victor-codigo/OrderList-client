import { Controller } from '@hotwired/stimulus';
import * as form from 'App/modules/form';
import * as communication from 'App/modules/ControllerCommunication'
import ModalManager from 'App/modules/ModalManager/ModalManager';
import * as FormItemPriceAddTrait from 'App/Twig/Components/Controls/ItemPriceAdd/ItemPriceAddFormTrait';
import { MODAL_CHAINS } from 'App/Config';
import * as autocomplete from 'App/modules/AutoComplete';
import * as geoApiFy from 'App/modules/GeoApiFy';
import * as url from 'App/modules/Url';

export default class ShopCreateController extends Controller {
    /**
     * @type {ModalManager|null}
     */
    #modalManager = null;
    get modalManager() { return this.#modalManager; }

    /**
     * @type {HTMLInputElement}
     */
    #addressTag;

    /**
     * @type {HTMLElement}
     */
    #itemPriceAddComponentTag;

    /**
     * @type {HTMLElement}
     */
    #dropzoneComponentTag;

    constructor(context) {
        Object.assign(ShopCreateController.prototype, FormItemPriceAddTrait);

        super(context);
    }

    /**
     * @this {ShopCreateController & FormItemPriceAddTrait}
     */
    connect() {
        this.#itemPriceAddComponentTag = this.element.querySelector('[data-controller="ItemPriceAddComponent"]');
        this.#dropzoneComponentTag = this.element.querySelector('[data-controller="DropZoneComponent"]');
        this.alertComponentTag = this.element.querySelector('[data-controller="AlertComponent"]');
        this.#addressTag = this.element.querySelector('[data-js-form-address]');

        autocomplete.create(
            () => geoApiFy.getAddresses(this.#addressTag.value, url.getLocale()),
            '[data-js-form-address]',
            1000, {
            showAllSuggestions: true
        });

        this.formValidate();
        this.setItemPriceAddEvents();
    }

    /**
     * @this {ShopCreateController & FormItemPriceAddTrait}
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

    #clearForm() {
        this.element.reset();
        this.element.classList.remove('was-validated');

        if (this.alertComponentTag !== null) {
            this.alertComponentTag.style.display = 'none';
        }

        this.#sendMessageClearToDropZone();
        this.#sendMessageItemPriceAddClear();
    }

    #setModalData() {
        if (this.modalManager.getChainCurrent() !== null
            && this.modalManager.getChainCurrent().getName() !== MODAL_CHAINS.shopCreateChain.name) {
            return;
        }

        this.#modalManager
            .addModal(MODAL_CHAINS.shopCreateChain.name, MODAL_CHAINS.shopCreateChain.modals.shopCreate.modalId)
            .addModal(MODAL_CHAINS.shopCreateChain.name, MODAL_CHAINS.shopCreateChain.modals.productList.modalId)
            .addModal(MODAL_CHAINS.shopCreateChain.name, MODAL_CHAINS.shopCreateChain.modals.productCreate.modalId)
        this.#modalManager.setModalCurrent(MODAL_CHAINS.shopCreateChain.name, MODAL_CHAINS.shopCreateChain.modals.shopCreate.modalId);
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {boolean} event.detail.content.showedFirstTime
     * @param {HTMLElement} event.detail.content.triggerElement
     * @param {ModalManager} event.detail.content.modalManager
     *
     * @this {ShopCreateController & FormItemPriceAddTrait}
     */
    handleMessageBeforeShowed({ detail: { content } }) {
        const modalBefore = content.modalManager.getModalOpenedBefore();
        this.#modalManager = content.modalManager;

        if (modalBefore === null) {
            this.#clearForm();
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

        this.modalManager.openNewModal(MODAL_CHAINS[chainCurrentName].modals.shopCreate.open.productsListModal, {
            itemsNotSelectable: content.itemsAdded
        });
    }

    handleMessageClear({ detail: { content } }) {
        this.#clearForm();
    }

    #sendMessageClearToDropZone() {
        communication.sendMessageToChildController(this.#dropzoneComponentTag, 'clear');
    }

    #sendMessageItemPriceAddClear() {
        if (this.#itemPriceAddComponentTag === null) {
            return;
        }

        communication.sendMessageToChildController(this.#itemPriceAddComponentTag, 'clear');
    }
}
