import { Controller } from '@hotwired/stimulus';
import * as apiEndpoint from 'App/modules/ApiEndpoints.js'
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
    #buttonCreateShop;

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
        this.#buttonCreateShop = this.element.querySelector('[data-controller="ButtonLoadingComponent"]');
        this.#nameTag = this.element.querySelector('[name="shop_create_form[name]"]');
        this.#backButtonTag = this.element.querySelector('[data-js-back-button]');

        this.#formTag = this.element.querySelector('[data-controller="ShopCreateComponent"]');
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

        const shopCreated = await this.#createShop();
        this.#sendMessageToButtonLoadingShowButton();

        if (typeof shopCreated === 'boolean') {
            return;
        }

        this.#modalShowOnSubmitOk(shopCreated, this.#nameTag.value);
    }

    /**
     * @param {string} shopNewId
     * @param {string} shopNewName
     */
    #modalShowOnSubmitOk(shopNewId, shopNewName) {
        const chainCurrentName = this.#modalManager.getChainCurrent().getName();

        this.#modalManager.openModalAlreadyOpened(MODAL_CHAINS[chainCurrentName].modals.productCreate.open.shopCreated, {
            itemData: {
                id: shopNewId.trim(),
                name: shopNewName.trim()
            }
        });
    }

    /**
     * @returns {Promise<(boolean|string)>}
     */
    async #createShop() {
        const responseData = await apiEndpoint.createShop(this.#formTag, this.#buttonCreateShop);

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
        communication.sendMessageToChildController(this.#buttonCreateShop, 'showButton');
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
        communication.sendMessageToChildController(this.#buttonCreateShop, 'showButton');
    }

    #sendMessageToButtonLoadingShowButtonLoading() {
        communication.sendMessageToChildController(this.#buttonCreateShop, 'showButtonLoading');
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
