import { Controller } from '@hotwired/stimulus';
import * as apiEndpoint from 'App/modules/ApiEndpoints.js'
import * as communication from 'App/modules/ControllerCommunication';
import * as alertComponent from 'App/Twig/Components/Alert/AlertComponent_controller';
import * as modal from 'App/modules/Modal';

export default class extends Controller {
    connect() {
        this.buttonCreateShop = this.element.querySelector('[data-controller="ButtonLoadingComponent"]');
        this.nameTag = this.element.querySelector('[name="shop_create_form[name]"]');
        this.descriptionTag = this.element.querySelector('[name="shop_create_form[description]"]');
        this.imageTag = this.element.querySelector('[name="shop_create_form[image]"]');
        this.tokenTag = this.element.querySelector('[name="shop_create_form[token]"]');
        this.backButtonTag = this.element.querySelector('[data-js-back-button]');

        this.formTag = this.element.querySelector('[data-controller="ShopCreateComponent"]');
        this.alertTag = this.element.querySelector('[data-controller="AlertComponent"]');

        this.formTag.addEventListener('submit', this.#submitFromHandler.bind(this));
    }

    disconnect() {
        this.formTag.removeEventListener('submit', this.#submitFromHandler);
    }

    /**
     * @param {Event} event
     */
    async #submitFromHandler(event) {
        event.preventDefault();

        if (!this.formTag.checkValidity()) {
            return;
        }

        this.#sendMessageToButtonLoadingShowButtonLoading();

        const shopCreated = await this.#createShop();
        this.#sendMessageToButtonLoadingShowButton();

        if (typeof shopCreated === 'boolean') {
            return;
        }

        this.#modalShowOnSubmitOk('product_create_modal', shopCreated, this.nameTag.value.trim());
    }

    /**
     * @param {string} modalAttributeId
     * @param {string} shopNewId
     * @param {string} shopNewName
     */
    #modalShowOnSubmitOk(modalAttributeId, shopNewId, shopNewName) {
        const modalCurrent = this.element.closest('[data-controller="ModalComponent"]');

        modal.closeCurrentAndOpenNew(modalCurrent, modalAttributeId, this.element, 'shopCreated', {
            shopId: shopNewId,
            shopName: shopNewName
        });
    }

    /**
     * @returns {Promise<(boolean|string)>}
     */
    async #createShop() {
        const responseData = await apiEndpoint.createShop(this.formTag, this.buttonCreateShop);

        if (responseData.status === 'ok') {
            this.#sendMessageToAlertComponent({
                ok: [responseData.message],
                errors: []
            });

            return responseData.data.id;
        }

        this.#sendMessageToAlertComponent({
            ok: [],
            errors: responseData.errors
        });

        return false;
    }

    /**
     * @param {string} modalBeforeAttributeId
     */
    #setModalBefore(modalBeforeAttributeId) {
        this.backButtonTag.dataset.bsTarget = '#' + modalBeforeAttributeId;
    }

    clear() {
        communication.sendMessageToChildController(this.buttonCreateShop, 'showButton');
    }

    /**
     * @param {object} validations
     * @param {string[]} validations.ok
     * @param {string[]} validations.errors
     */
    #sendMessageToAlertComponent(validations) {
        communication.sendMessageToChildController(this.alertTag, 'setValidations', {
            validations: validations,
            type: validations.ok.length === 0 ? alertComponent.ALERT_TYPE.DANGER : alertComponent.ALERT_TYPE.SUCCESS
        });
    }

    #sendMessageToButtonLoadingShowButton() {
        communication.sendMessageToChildController(this.buttonCreateShop, 'showButton');
    }

    #sendMessageToButtonLoadingShowButtonLoading() {
        communication.sendMessageToChildController(this.buttonCreateShop, 'showButtonLoading');
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {boolean} event.detail.content.showedFirstTime
     * @param {HTMLElement} event.detail.content.triggerElement
     */
    handleMessageBeforeShowed({ detail: { content } }) {
        if (typeof content.triggerElement.dataset.modalCurrent !== 'undefined') {
            this.#setModalBefore(content.triggerElement.dataset.modalCurrent);
        }

        this.clear();
    }
}
