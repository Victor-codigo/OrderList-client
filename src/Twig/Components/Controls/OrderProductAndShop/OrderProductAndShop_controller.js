import { Controller } from '@hotwired/stimulus';
import * as communication from 'App/modules/ControllerCommunication';
import * as apiEndpoint from 'App/modules/ApiEndpoints';
import * as locale from 'App/modules/Locale';

const PRODUCT_SHOPS_MAX = 100;

export default class extends Controller {
    /**
     * @type {HTMLInputElement}
     */
    #productSelectNameTag;

    /**
     * @type {HTMLInputElement}
     */
    #productSelectIdTag;

    /**
     * @type {HTMLButtonElement}
     */
    #productSelectButtonTag;

    /**
     * @type {HTMLSelectElement}
     */
    #shopSelectTag;

    /**
     * @type {HTMLElement}
     */
    #productPriceTag;

    /**
     * @type {string}
     */
    #groupId;

    /**
     * @type {string}
     */
    #productId;

    /**
     * @type {string}
     */
    #shopId;

    /**
     * @type {number}
     */
    #price;

    /**
     * @type {string}
     */
    #unit;

    connect() {
        this.#productSelectIdTag = this.element.querySelector('[data-js-product-id-select]');
        this.#productSelectNameTag = this.element.querySelector('[data-js-product-select-name]');
        this.#productSelectButtonTag = this.element.querySelector('[data-js-product-select-button]');
        this.#shopSelectTag = this.element.querySelector('[data-js-shop-select]');
        this.#productPriceTag = this.element.querySelector('[data-js-product-price]');

        this.#productSelectButtonTag.addEventListener('click', this.#sendMessageProductSelectClickedToParent.bind(this));
        this.#shopSelectTag.addEventListener('change', this.#handleShopChangeEvent.bind(this));
    }

    disconnect() {
        this.#productSelectButtonTag.removeEventListener('click', this.#sendMessageProductSelectClickedToParent);
        this.#shopSelectTag.removeEventListener('change', this.#handleShopChangeEvent);
    }

    /**
     * @param {string} groupId
     * @param {string} productId
     * @param {string} productName
     * @param {string|null} shopId
     */
    async #setProductData(groupId, productId, productName, shopId) {
        this.#clearProductShopPriceData();
        this.#productSelectIdTag.value = productId;
        this.#productSelectNameTag.value = productName;
        await this.#loadProductShops(groupId, productId, shopId);
        await this.#loadProductShopPrice(groupId, productId, this.#shopSelectTag.value);


        this.#sendMessageShopSelectedTpParent();
    }

    /**
     * @param {string} groupId
     * @param {string} productId
     * @param {string|null} shopIdSelected
     */
    async #loadProductShops(groupId, productId, shopIdSelected) {
        const loadingSpinner = this.element.querySelector('[data-js-spinner]');
        loadingSpinner.hidden = false;

        const productShopsData = await apiEndpoint.getShopsData(
            groupId,
            1,
            PRODUCT_SHOPS_MAX,
            null,
            [productId],
            null,
            null,
            null,
            true,
        );

        const options = productShopsData.shops.map((shop) => {
            const option = document.createElement('option');

            if (shopIdSelected === shop.id) {
                option.selected = true;
            }

            option.value = shop.id;
            option.textContent = shop.name;

            return option;
        });
        this.#shopSelectTag.replaceChildren(...options);

        loadingSpinner.hidden = true;
        this.#shopSelectTag.disabled = options.length === 0;

        this.#groupId = groupId;
        this.#productId = productId;
    }

    /**
     * @param {string} groupId
     * @param {string} productId
     * @param {string} shopId
     */
    async #loadProductShopPrice(groupId, productId, shopId) {
        try {
            const productsShopsPrice = await apiEndpoint.getProductShopsPricesData(groupId, [productId], []);
            const productShopPriceCurrent = productsShopsPrice.products_shops_prices.find((productShopPrice) => productShopPrice.shop_id === shopId);

            this.#productPriceTag.textContent = '';
            this.#unit = '';
            if (productShopPriceCurrent.price !== null) {
                this.#productPriceTag.textContent = locale.formatPriceCurrencyAndUnit(productShopPriceCurrent.price, productShopPriceCurrent.unit);
                this.#unit = productShopPriceCurrent.unit
            }

            this.#shopId = shopId;
            this.#price = parseFloat(productShopPriceCurrent.price);
        } catch (Error) {
            this.#productPriceTag.innerHTML = '';
        }
    }

    #clear() {
        this.#productSelectNameTag.value = '';
        this.#productSelectIdTag.value = '';
        this.#shopSelectTag.innerHTML = '';
        this.#shopSelectTag.disabled = true;
        this.#productPriceTag.innerHTML = '';

        this.#clearProductShopPriceData();
    }

    #clearProductShopPriceData() {
        this.#groupId = '';
        this.#productId = '';
        this.#clearShopPriceData();
    }

    #clearShopPriceData() {
        this.#shopId = '';
        this.#price = 0;
        this.#unit = '';
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {string} event.detail.content.groupId
     * @param {string} event.detail.content.productId
     * @param {string} event.detail.content.productName
     * @param {string} event.detail.content.shopId
     */
    handleMessageSetProductShopData({ detail: { content: { groupId, productId, productName, shopId } } }) {
        this.#setProductData(groupId, productId, productName, shopId);
    }

    handleMessageClear() {
        this.#clear();
    }

    /**
     * @param {Event} event
     */
    async #handleShopChangeEvent(event) {
        this.#clearShopPriceData();
        if (event.currentTarget instanceof HTMLSelectElement) {
            await this.#loadProductShopPrice(this.#groupId, this.#productId, event.currentTarget.value);
        }

        this.#sendMessageShopSelectedTpParent();
    }

    #sendMessageProductSelectClickedToParent() {
        communication.sendMessageToParentController(this.element, 'productSelected');
    }

    #sendMessageShopSelectedTpParent() {
        communication.sendMessageToParentController(this.element, 'shopSelected', {
            groupId: this.#groupId,
            productId: this.#productId,
            shopId: this.#shopId,
            price: this.#price,
            unit: this.#unit
        });
    }
}