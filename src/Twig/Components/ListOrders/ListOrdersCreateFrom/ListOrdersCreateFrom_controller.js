import { Controller } from '@hotwired/stimulus';
import * as form from 'App/modules/form';
import ModalManager from 'App/modules/ModalManager/ModalManager';
import { MODAL_CHAINS } from 'App/Config';

export default class extends Controller {

    /**
     * @type {ModalManager|null}
     */
    #modalManager = null;

    /**
     * @type {HTMLInputElement}
     */
    #listOrdersSelectName;

    /**
     * @type {HTMLButtonElement}
     */
    #listOrdersSelectButton;

    /**
     * @type {HTMLInputElement}
     */
    #listOrdersCreateFrom;

    connect() {
        this.#listOrdersSelectName = this.element.querySelector('[data-js-list-orders-select-name]');
        this.#listOrdersSelectButton = this.element.querySelector('[data-js-list-orders-select-button]');
        this.#listOrdersCreateFrom = this.element.querySelector('[data-js-list-orders-create-from]');

        this.element.addEventListener('submit', this.#handleFormSubmit.bind(this));
        this.#listOrdersSelectButton.addEventListener('click', this.#openListOrdersSelect.bind(this));

        this.formValidate();
    }

    disconnect() {
        this.element.removeEventListener('click', this.#handleFormSubmit);
        this.#listOrdersSelectButton.removeEventListener('click', this.#openListOrdersSelect);
    }

    formValidate() {
        form.validate(this.element, null);
    }

    /**
     * @param {Event} event
     */
    #handleFormSubmit(event) {
        const valid = this.#validateListOrdersInput();

        if (!valid || !this.element.checkValidity()) {
            event.preventDefault();
        }
    }

    /**
     * @return {boolean}
     */
    #validateListOrdersInput() {
        this.#listOrdersSelectName.readOnly = false;

        if (!this.#listOrdersSelectName.checkValidity()) {
            this.#listOrdersSelectName.classList.add('is-invalid');
            this.#listOrdersSelectName.readOnly = true;

            return false;
        }

        this.#listOrdersSelectName.classList.remove('is-invalid');
        this.#listOrdersSelectName.readOnly = true;

        return true;
    }

    #setModalData() {
        if (this.#modalManager.getChainCurrent() !== null
            && this.#modalManager.getChainCurrent().getName() !== MODAL_CHAINS.listOrdersCreateFromChain.name) {
            return;
        }

        this.#modalManager
            .addModal(MODAL_CHAINS.listOrdersCreateFromChain.name, MODAL_CHAINS.listOrdersCreateFromChain.modals.listOrdersCreateFrom.modalId)
            .addModal(MODAL_CHAINS.listOrdersCreateFromChain.name, MODAL_CHAINS.listOrdersCreateFromChain.modals.listOrdersList.modalId)
        this.#modalManager.setModalCurrent(MODAL_CHAINS.listOrdersCreateFromChain.name, MODAL_CHAINS.listOrdersCreateFromChain.modals.listOrdersCreateFrom.modalId);
    }

    #openListOrdersSelect() {
        this.#modalManager.openNewModal(MODAL_CHAINS.listOrdersCreateFromChain.modals.listOrdersList.modalId, {}, null);
    }

    /**
     * @returns {{id: string|null, name: string|null}}
     */
    getModalBeforeSharedData() {
        const modalBeforeSharedData = this.#modalManager.getModalOpenedBeforeData();
        const returnDefault = {
            id: null,
            name: null
        };

        if (typeof modalBeforeSharedData.itemData === 'undefined') {
            return null;
        }

        return { ...returnDefault, ...modalBeforeSharedData.itemData };
    }

    #clearForm() {
        this.element.reset();
        this.element.classList.remove('was-validated');
    }

    /**
     * @param {string} id
     * @param {string} name
     */
    #setListOrdersSelectedData(id, name) {
        this.#listOrdersCreateFrom.value = id;
        this.#listOrdersSelectName.value = name;
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
        const modalBefore = content.modalManager.getModalOpenedBefore();
        this.#modalManager = content.modalManager;

        if (modalBefore === null) {
            this.#clearForm();
            this.#setModalData();

            return;
        }

        const modalBeforeSharedData = this.getModalBeforeSharedData();

        if (modalBeforeSharedData !== null) {
            this.#setListOrdersSelectedData(modalBeforeSharedData.id, modalBeforeSharedData.name);
        }

    }
}

