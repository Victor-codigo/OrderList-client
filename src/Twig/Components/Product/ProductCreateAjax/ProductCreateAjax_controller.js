import { Controller } from '@hotwired/stimulus';
import * as apiEndpoint from 'App/modules/ApiEndpoints';
import * as communication from 'App/modules/ControllerCommunication';
import * as alertComponent from 'App/Twig/Components/Alert/AlertComponent_controller';
import ModalManager from 'App/modules/ModalManager/ModalManager';
import { MODAL_CHAINS } from 'App/Config';

export default class extends Controller {
    /**
     * @type {ModalManager}
     */
    #modalManager;

    /**
     * @type {HTMLButtonElement}
     */
    #buttonCreateProduct;

    /**
     * @type {HTMLInputElement}
     */
    #nameTag;

    /**
     * @type {HTMLButtonElement}
     */
    #backButtonTag;

    /**
     * @type {HTMLFormElement}
     */
    #formTag;

    /**
     * @type {HTMLElement}
     */
    #alertTag;

    connect() {
        this.#buttonCreateProduct = this.element.querySelector('[data-controller="ButtonLoadingComponent"]');
        this.#nameTag = this.element.querySelector('[name="product_create_form[name]"]');
        this.#backButtonTag = this.element.querySelector('[data-js-back-button]');

        this.#formTag = this.element.querySelector('[data-controller="ProductCreateComponent"]');
        this.#alertTag = this.element.querySelector('[data-controller="AlertComponent"]');

        this.#formTag.addEventListener('submit', this.#submitFromHandler.bind(this));
        this.#backButtonTag.addEventListener('click', this.#openModalBefore.bind(this));
    }

    disconnect() {
        this.#formTag.removeEventListener('submit', this.#submitFromHandler);
        this.#backButtonTag.removeEventListener('click', this.#openModalBefore);
    }

    /**
     * @param {Event} event
     */
    #openModalBefore(event) {
        this.#modalManager.openModalBefore();
    }

    /**
     * @param {Event} event
     */
    async #submitFromHandler(event) {
        event.preventDefault();

        if (!this.#formTag.checkValidity()) {
            return;
        }

        this.#sendMessageToButtonLoadingShowButtonLoading();

        const productCreated = await this.#createProduct();
        this.#sendMessageToButtonLoadingShowButton();

        if (typeof productCreated === 'boolean') {
            return;
        }

        this.#modalShowOnSubmitOk(productCreated, this.#nameTag.value);
    }

    /**
     * @param {string} productNewId
     * @param {string} productNewName
     */
    #modalShowOnSubmitOk(productNewId, productNewName) {
        const chainCurrentName = this.#modalManager.getChainCurrent().getName();

        this.#modalManager.openModalAlreadyOpened(MODAL_CHAINS[chainCurrentName].modals.productCreate.open.productCreated, {
            itemData: {
                id: productNewId.trim(),
                name: productNewName.trim()
            }
        });
    }

    /**
     * @returns {Promise<(boolean|string)>}
     */
    async #createProduct() {
        const responseData = await apiEndpoint.createProduct(this.#formTag, this.#buttonCreateProduct);

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

    clear() {
        this.element.querySelector('[data-controller="AlertComponent"]')?.remove();
        communication.sendMessageToChildController(this.#buttonCreateProduct, 'showButton');
    }

    /**
     * @param {object} validations
     * @param {string[]} validations.ok
     * @param {string[]} validations.errors
     */
    #sendMessageToAlertComponent(validations) {
        communication.sendMessageToChildController(this.#alertTag, 'setValidations', {
            validations: validations,
            type: validations.ok.length === 0 ? alertComponent.ALERT_TYPE.DANGER : alertComponent.ALERT_TYPE.SUCCESS
        });
    }

    #sendMessageToButtonLoadingShowButton() {
        communication.sendMessageToChildController(this.#buttonCreateProduct, 'showButton');
    }

    #sendMessageToButtonLoadingShowButtonLoading() {
        communication.sendMessageToChildController(this.#buttonCreateProduct, 'showButtonLoading');
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {boolean} event.detail.content.showedFirstTime
     * @param {ModalManager} event.detail.content.modalManager
     */
    handleMessageBeforeShowed({ detail: { content } }) {
        this.#modalManager = content.modalManager;
        this.clear();
    }
}
