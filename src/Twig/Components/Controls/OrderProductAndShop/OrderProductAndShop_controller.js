import { Controller } from '@hotwired/stimulus';
import * as communication from 'App/modules/ControllerCommunication';
import * as apiEndpoint from 'App/modules/ApiEndpoints';
import * as locale from 'App/modules/Locale';
import * as config from 'App/Config';

const PRODUCT_SHOPS_MAX = config.PAGINATION_ITEMS_MAX;

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


    /**
     * @type {object[]}
     */
    #productShopsData;

    /**
     * @type {[{
     *  price: number,
     *  product_id: string,
     *  shop_id: string,
     *  unit: string
     * }]}
     */
    #productShopsPrices;

    connect() {
        this.#productSelectIdTag = this.element.querySelector('[data-js-product-id-select]');
        this.#productSelectNameTag = this.element.querySelector('[data-js-product-select-name]');
        this.#productSelectButtonTag = this.element.querySelector('[data-js-product-select-button]');
        this.#shopSelectTag = this.element.querySelector('[data-js-shop-select]');
        this.#productPriceTag = this.element.querySelector('[data-js-product-price]');

        this.#productSelectButtonTag.addEventListener('click', this.#sendMessageProductSelectClickedToParent.bind(this));
        this.#shopSelectTag.addEventListener('change', this.#handleShopChangeEvent.bind(this));
        this.#shopSelectTag.addEventListener('blur', this.#handleShopChangeEvent.bind(this));
        this.#shopSelectTag.addEventListener('mousedown', this.#handleShopClickEvent.bind(this));
    }

    disconnect() {
        this.#productSelectButtonTag.removeEventListener('click', this.#sendMessageProductSelectClickedToParent);
        this.#shopSelectTag.removeEventListener('change', this.#handleShopChangeEvent);
        this.#shopSelectTag.removeEventListener('blur', this.#handleShopChangeEvent);
        this.#shopSelectTag.removeEventListener('mousedown', this.#handleShopChangeEvent);
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
        this.#productShopsPrices = await this.#loadProductShopPrices(groupId, productId);
        await this.#loadProductShops(groupId, productId, shopId);
        this.#setShopPrice(this.#productShopsPrices, this.#shopSelectTag.value);

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

        this.#productShopsData = productShopsData.shops;
        loadingSpinner.hidden = true;
        this.#setShopSelectTagOptions(this.#productShopsData, shopIdSelected, false);

        this.#groupId = groupId;
        this.#productId = productId;
    }

    /**
     * @param {{
     *  productShopPrice: {
     *      price: number,
    *       product_id: string,
    *       shop_id: string,
    *       unit: string
    * }}} shop1
    * @param {{
    *   productShopPrice: {
     *      price: number,
    *       product_id: string,
    *       shop_id: string,
    *       unit: string
    * }}} shop2
     */
    #sortShopByProductPriceAscendant(shop1, shop2) {
        if (shop1.productShopPrice.price === null) {
            return 1;
        }

        if (shop2.productShopPrice.price === null) {
            return -1;
        }

        if (shop1.productShopPrice.price === null && shop2.productShopPrice.price === null) {
            return 0;
        }

        return shop1.productShopPrice.price - shop2.productShopPrice.price;
    }

    /**
     * @param {object[]} shops
     * @param {string} shopIdSelected
     * @param {boolean} setPrice
     */
    #setShopSelectTagOptions(shops, shopIdSelected, setPrice) {
        let shopsWhitPrice = shops;

        if (setPrice) {
            shopsWhitPrice = shops.map((shop) => {
                const productShopPrice = this.#getProductShopPriceById(shop.id);
                shop.productShopPrice = productShopPrice;

                return shop;
            });
            shopsWhitPrice = shopsWhitPrice.sort(this.#sortShopByProductPriceAscendant);
        }

        const options = shopsWhitPrice.map((shop) => {
            const option = document.createElement('option');

            if (shopIdSelected === shop.id) {
                option.selected = true;
            }

            let shopName = shop.name;
            if (setPrice) {
                if (shop.productShopPrice.price !== null) {
                    const price = locale.formatPriceCurrencyAndUnit(shop.productShopPrice.price, shop.productShopPrice.unit);

                    shopName += ` - ${price}`;
                }
            }

            option.value = shop.id;
            option.textContent = shopName;

            return option;
        });

        this.#shopSelectTag.replaceChildren(...options);
        this.#shopSelectTag.disabled = options.length === 0;
    }

    /**
     * @param {string} shopId
     *
     * @returns {null|{
     *  price: number,
     *  product_id: string,
     *  shop_id: string,
     *  unit: string
     * }}
     */
    #getProductShopPriceById(shopId) {
        const shopPrice = this.#productShopsPrices.filter((productShopPrice) => productShopPrice.shop_id === shopId);

        if (shopPrice.length === 0) {
            return null;
        }

        return shopPrice[0];
    }

    /**
     * @param {string} groupId
     * @param {string} productId
     *
     * @returns {Promise<[{
     *  price: number,
     *  product_id: string,
     *  shop_id: string,
     *  unit: string
     * }]>}
     */
    async #loadProductShopPrices(groupId, productId) {
        let productsShopsPrice = await apiEndpoint.getProductShopsPricesData(groupId, [productId], []);

        return productsShopsPrice.products_shops_prices;
    }

    /**
     * @param {[{
     *  price: number,
     *  product_id: string,
     *  shop_id: string,
     *  unit: string
     * }]} productsShopsPrice,
     * @param {string} shopId
     */
    #setShopPrice(productsShopsPrice, shopId) {
        const productShopPriceCurrent = productsShopsPrice.find((productShopPrice) => productShopPrice.shop_id === shopId);

        this.#productPriceTag.textContent = '';
        this.#unit = '';

        if (typeof productShopPriceCurrent === 'undefined') {
            return;
        }

        if (productShopPriceCurrent.price !== null) {
            this.#productPriceTag.textContent = locale.formatPriceCurrencyAndUnit(productShopPriceCurrent.price, productShopPriceCurrent.unit);
            this.#unit = productShopPriceCurrent.unit
        }

        this.#shopId = shopId;
        this.#price = productShopPriceCurrent.price;
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
        this.#setShopPrice(this.#productShopsPrices, event.currentTarget.value);
        this.#sendMessageShopSelectedTpParent();
        this.#setShopSelectTagOptions(this.#productShopsData, event.currentTarget.value, false);
    }

    /**
     * @param {PointerEvent} event
     */
    #handleShopClickEvent(event) {
        this.#setShopSelectTagOptions(this.#productShopsData, event.currentTarget.value, true);
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