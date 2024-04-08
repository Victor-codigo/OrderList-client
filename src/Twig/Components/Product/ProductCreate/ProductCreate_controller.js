import { Controller } from '@hotwired/stimulus';
import * as form from 'App/modules/form';
import * as communication from 'App/modules/ControllerCommunication';
import ModalManager from 'App/modules/ModalManager/ModalManager';
import * as FormItemPriceAddTrait from 'App/Twig/Components/Controls/ItemPriceAdd/ItemPriceAddFormTrait';
import { MODAL_CHAINS } from 'App/Config';

export default class ProductCreateController extends Controller {
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
     * @type {HTMLElement}
     */
    #dropzoneComponentTag;

    constructor(context) {
        Object.assign(ProductCreateController.prototype, FormItemPriceAddTrait);

        super(context);
    }

    /**
     * @this {ProductCreateController & FormItemPriceAddTrait}
     */
    connect() {
        this.#itemPriceAddComponentTag = this.element.querySelector('[data-controller="ItemPriceAddComponent"]');
        this.#dropzoneComponentTag = this.element.querySelector('[data-controller="DropZoneComponent"]');

        this.formValidate();
        this.setItemPriceAddEvents();
    }

    /**
     * @this {ProductCreateController & FormItemPriceAddTrait}
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
        this.#sendMessageDropzoneClear();
        this.#sendMessageItemPriceAddClear();
    }

    #setModalData() {
        if (this.modalManager.getChainCurrent() !== null
            && this.modalManager.getChainCurrent().getName() !== MODAL_CHAINS.productCreateChain.name) {
            return;
        }

        this.#modalManager
            .addModal(MODAL_CHAINS.productCreateChain.name, MODAL_CHAINS.productCreateChain.modals.productCreate.modalId)
            .addModal(MODAL_CHAINS.productCreateChain.name, MODAL_CHAINS.productCreateChain.modals.shopList.modalId)
            .addModal(MODAL_CHAINS.productCreateChain.name, MODAL_CHAINS.productCreateChain.modals.shopCreate.modalId)
        this.#modalManager.setModalCurrent(MODAL_CHAINS.productCreateChain.name, MODAL_CHAINS.productCreateChain.modals.productCreate.modalId);
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {boolean} event.detail.content.showedFirstTime
     * @param {HTMLElement} event.detail.content.triggerElement
     * @param {ModalManager} event.detail.content.modalManager
     *
     * @this {ProductCreateController & FormItemPriceAddTrait}
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

        this.modalManager.openNewModal(MODAL_CHAINS[chainCurrentName].modals.productCreate.open.shopList, {
            itemsNotSelectable: content.itemsAdded
        });
    }

    #sendMessageItemPriceAddClear() {
        if (this.#itemPriceAddComponentTag === null) {
            return;
        }

        communication.sendMessageToChildController(this.#itemPriceAddComponentTag, 'clear');
    }

    #sendMessageDropzoneClear() {
        communication.sendMessageToChildController(this.#dropzoneComponentTag, 'clear')
    }
}


