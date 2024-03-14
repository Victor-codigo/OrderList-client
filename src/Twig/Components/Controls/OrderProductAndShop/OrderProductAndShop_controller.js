import { Controller } from '@hotwired/stimulus';
import * as communication from 'App/modules/ControllerCommunication';
import * as apiEndpoint from 'App/modules/ApiEndpoints';
import * as html from 'App/modules/Html';
import * as config from 'App/Config';

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
     */
    async #setProductData(groupId, productId, productName) {
        this.#clearProductShopPriceData();
        await this.#loadProductShops(groupId, productId);
        await this.#loadProductShopPrice(groupId, productId, this.#shopSelectTag.value);

        this.#productSelectIdTag.value = productId;
        this.#productSelectNameTag.value = productName;

        this.#sendMessageShopSelectedTpParent();
    }

    /**
     * @param {string} groupId
     * @param {string} productId
     */
    async #loadProductShops(groupId, productId) {
        const loadingSpinner = this.element.querySelector('[data-js-spinner]');
        loadingSpinner.hidden = false;

        const productShopsData = await apiEndpoint.getShopsData({
            'group_id': groupId,
            'page': 1,
            'page_items': PRODUCT_SHOPS_MAX,
            'order_asc': true,
            'products_id': productId
        });

        const options = productShopsData.shops.map((shop) => {
            const option = document.createElement('option');

            option.value = shop.id;
            option.innerHTML = html.escape(shop.name);

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

            this.#productPriceTag.innerHTML = '';
            this.#unit = '';
            if (productShopPriceCurrent.price !== null) {
                this.#productPriceTag.innerHTML = html.escape(`${productShopPriceCurrent.price}${config.CURRENCY}/${config.UNIT_MEASURE.translate(productShopPriceCurrent.unit, false)}`);
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
     */
    handleMessageSetProductData({ detail: { content: { groupId, productId, productName } } }) {
        this.#setProductData(groupId, productId, productName);
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