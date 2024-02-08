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

    #clearForm() {
        this.element.reset();
        this.element.classList.remove('was-validated');
        this.#sendMessageDropzoneClear();
        this.#sendMessageItemPriceAddClear();
    }

    #setModalData() {
        if (!this.#modalManager.modalChainExists(MODAL_CHAINS.productCreateChain.name)) {
            this.#modalManager
                .addModal(MODAL_CHAINS.productCreateChain.name, MODAL_CHAINS.productCreateChain.modals.productCreate)
                .addModal(MODAL_CHAINS.productCreateChain.name, MODAL_CHAINS.productCreateChain.modals.shopList)
                .addModal(MODAL_CHAINS.productCreateChain.name, MODAL_CHAINS.productCreateChain.modals.shopCreate)
        }

        this.#modalManager.setModalCurrent(MODAL_CHAINS.productCreateChain.name, MODAL_CHAINS.productCreateChain.modals.productCreate);
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
        this.#setModalData();

        if (modalBefore === null) {
            this.#clearForm();

            return;
        }

        const modalBeforeSharedData = this.getModalBeforeSharedData();

        if (modalBeforeSharedData !== null) {
            this.setItemCurrentData(modalBeforeSharedData.id, modalBeforeSharedData.name);
        }

    }

    #sendMessageItemPriceAddClear() {
        communication.sendMessageToChildController(this.#itemPriceAddComponentTag, 'clear');
    }

    #sendMessageDropzoneClear() {
        communication.sendMessageToChildController(this.#dropzoneComponentTag, 'clear')
    }
}


