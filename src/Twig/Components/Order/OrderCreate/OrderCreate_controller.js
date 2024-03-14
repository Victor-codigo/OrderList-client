import { Controller } from '@hotwired/stimulus';
import * as form from 'App/modules/form';
import * as communication from 'App/modules/ControllerCommunication';
import ModalManager from 'App/modules/ModalManager/ModalManager';
import * as html from 'App/modules/Html';
import { MODAL_CHAINS, CURRENCY, UNIT_MEASURE } from 'App/Config';

export default class ProductCreateController extends Controller {
    /**
     * @type {ModalManager|null}
     */
    #modalManager = null;
    get modalManager() { return this.#modalManager; }

    /**
     * @type {HTMLElement}
     */
    #orderProductAndShopTag;

    /**
     * @type {HTMLInputElement}
     */
    #productSelectName;

    /**
     * @type {HTMLInputElement}
     */
    #amountInputTag;

    /**
     * @type {HTMLElement}
     */
    #amountUnitTag;

    /**
     * @type {HTMLElement}
     */
    #totalTag;

    /**
     * @type {{
     *      groupId: string,
     *      productId: string,
     *      shopId: string,
     *      price: number,
     *      unit: string
     * }}
     */
    #productShopData = {
        groupId: '',
        productId: '',
        shopId: '',
        price: 0,
        unit: ''
    };

    connect() {
        this.#orderProductAndShopTag = this.element.querySelector('[data-controller="OrderProductAndShopComponent"]');
        this.#productSelectName = this.#orderProductAndShopTag.querySelector('[data-js-product-select-name]');
        this.#amountInputTag = this.element.querySelector('[data-js-amount]');
        this.#totalTag = this.element.querySelector('[data-js-total]');
        this.#amountUnitTag = this.element.querySelector('[data-js-amount-unit]');

        this.element.addEventListener('submit', this.#handleFormSubmit.bind(this));
        this.#amountInputTag.addEventListener('input', this.#handleAmountInputInputEvent.bind(this));

        this.formValidate();
    }


    disconnect() {
        this.element.addEventListener('submit', this.#handleFormSubmit);
        this.#amountInputTag.removeEventListener('input', this.#handleAmountInputInputEvent);
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
    }

    #setModalData() {
        if (this.modalManager.getChainCurrent() !== null
            && this.modalManager.getChainCurrent().getName() !== MODAL_CHAINS.orderCreateChain.name) {
            return;
        }

        this.#modalManager
            .addModal(MODAL_CHAINS.orderCreateChain.name, MODAL_CHAINS.orderCreateChain.modals.orderCreate.modalId)
            .addModal(MODAL_CHAINS.orderCreateChain.name, MODAL_CHAINS.orderCreateChain.modals.orderProductList.modalId)
        this.#modalManager.setModalCurrent(MODAL_CHAINS.orderCreateChain.name, MODAL_CHAINS.orderCreateChain.modals.orderCreate.modalId);
    }

    /**
    * @returns {{id: string|null, name: string|null, itemsAdded: string[]}}
    */
    #getModalBeforeSharedData() {
        const modalBeforeSharedData = this.modalManager.getModalOpenedBeforeData();
        const returnDefault = {
            id: null,
            name: null
        };

        if (typeof modalBeforeSharedData.itemData === 'undefined') {
            return null;
        }

        return { ...returnDefault, ...modalBeforeSharedData.itemData };
    }

    /**
     * @param {number} price
     */
    #calculatePrice(price) {
        const amount = parseFloat(this.#amountInputTag.value);
        const totalPrice = price * amount;

        if (!isNaN(totalPrice) && totalPrice >= 0) {
            this.#totalTag.innerHTML = html.escape(totalPrice.toLocaleString() + CURRENCY);

            return;
        }

        this.#totalTag.innerHTML = '0' + CURRENCY;
    }

    /**
     * @param {string} unit
     */
    #setAmountUnit(unit) {
        this.#amountUnitTag.innerText = UNIT_MEASURE.translate(unit, true);
    }

    /**
     * @return {boolean}
     */
    #validateProductInput() {
        this.#productSelectName.readOnly = false;

        if (!this.#productSelectName.checkValidity()) {
            this.#productSelectName.classList.add('is-invalid');
            this.#productSelectName.readOnly = true;

            return false;
        }

        this.#productSelectName.classList.remove('is-invalid');
        this.#productSelectName.readOnly = true;

        return true;
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
            this.#sendMessageClearToOrderProductAndShop();
            this.#calculatePrice(0)

            return;
        }

        const modalBeforeSharedData = this.#getModalBeforeSharedData();

        if (modalBeforeSharedData !== null) {
            this.#sendMessageSetProductDataToOrderProductAndShop(
                this.element.dataset.groupId,
                modalBeforeSharedData.id,
                modalBeforeSharedData.name
            );
        }
    }

    handleOrderProductSelected() {
        const chainCurrentName = this.modalManager.getChainCurrent().getName();

        this.modalManager.openNewModal(MODAL_CHAINS[chainCurrentName].modals.orderCreate.open.productsListModal);
    }

    /**
     * @param {Object} data
     * @param {object} data.detail
     * @param {object} data.detail.content
     * @param {string} data.detail.content.groupId
     * @param {string} data.detail.content.productId
     * @param {string} data.detail.content.shopId
     * @param {number} data.detail.content.price
     * @param {string} data.detail.content.unit
     */
    handleOrderShopSelected({ detail: { content: { groupId, productId, shopId, price, unit } } }) {
        this.#productShopData = {
            groupId: groupId,
            productId: productId,
            shopId: shopId,
            price: price,
            unit: unit,
        };
        this.#calculatePrice(price);
        this.#setAmountUnit(unit);
        this.#validateProductInput();
    }

    /**
     * @param {Event} event
     */
    #handleAmountInputInputEvent(event) {
        this.#calculatePrice(this.#productShopData.price);
    }

    /**
     * @param {Event} event
     */
    #handleFormSubmit(event) {
        const valid = this.#validateProductInput();

        if (!valid || !this.element.checkValidity()) {
            event.preventDefault();
        }
    }

    /**
     * @param {string} groupId
     * @param {string} itemId
     * @param {string} itemName
     */
    #sendMessageSetProductDataToOrderProductAndShop(groupId, itemId, itemName) {
        communication.sendMessageToChildController(this.#orderProductAndShopTag, 'setProductData', {
            groupId: groupId,
            productId: itemId,
            productName: itemName
        })
    }

    #sendMessageClearToOrderProductAndShop() {
        communication.sendMessageToChildController(this.#orderProductAndShopTag, 'clear');
    }
}