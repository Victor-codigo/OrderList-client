import ItemInfo_controller from 'App/Twig/Components/HomeSection/ItemInfo/ItemInfo_controller';
import * as config from 'App/Config';
import * as locale from 'App/modules/Locale';

/**
 * @typedef {config.ItemData} ListOrdersData
 * @property {float} amount
 * @property {boolean} bought
 */

export default class extends ItemInfo_controller {

    /**
     * @type {HTMLParagraphElement}
     */
    #amountTag;

    /**
     * @type {HTMLImageElement}
     */
    #boughtTag;

    /**
     * @type {HTMLButtonElement}
     */
    #productNameTag;

    /**
     * @type {HTMLParagraphElement}
     */
    #productDescriptionTag;

    /**
     * @type {HTMLButtonElement}
     */
    #shopNameTag;

    /**
     * @type {HTMLParagraphElement}
     */
    #shopDescriptionTag;

    /**
     * @type {HTMLParagraphElement}
     */
    #shopPriceTag;

    /**
     * @type {HTMLParagraphElement}
     */
    #priceTotalTag;

    /**
     * @type {HTMLDivElement}
     */
    #shopInfoTag;


    connect() {
        super.connect();

        this.#amountTag = this.element.querySelector('[data-js-item-amount]');
        this.#boughtTag = this.element.querySelector('[data-js-item-bought]');
        this.#productNameTag = this.element.querySelector('[data-js-item-product-name]');
        this.#productDescriptionTag = this.element.querySelector('[data-js-item-product-description]');
        this.#shopNameTag = this.element.querySelector('[data-js-item-shop-name]');
        this.#shopDescriptionTag = this.element.querySelector('[data-js-item-shop-description]');
        this.#shopPriceTag = this.element.querySelector('[data-js-item-shop-price]');
        this.#priceTotalTag = this.element.querySelector('[data-js-item-shop-price-total]');
        this.#shopInfoTag = this.element.querySelector('[data-js-shop-info]');
    }

    /**
     * @param {ListOrdersData} data
     */
    setItemData(data) {
        console.log(data);
        this.titleTag.innerText = data.product.name;
        this.imageTag.src = data.image;
        this.dateTag.innerText = locale.formatDateToLocale(data.createdOn);
        this.descriptionTag.innerText = data.description;
        this.#setBoughtImage(data.bought);
        this.#amountTag.innerText = locale.formatAmountAndUnit(data.amount, data.productShop.unit);

        this.#productNameTag.innerText = data.product.name;
        this.#productDescriptionTag.innerText = data.product.description;

        this.#shopNameTag.innerText = data.shop.name;
        this.#shopDescriptionTag.innerText = data.shop.description;
        this.#shopPriceTag.innerText = locale.formatPriceCurrencyAndUnit(data.productShop.price, data.productShop.unit);

        this.#priceTotalTag.innerText = locale.formatToStringLocaleCurrency(this.#calculatePriceTotal(data.productShop.price, data.amount));
        this.#setShopInfo(data.shop);
    }

    /**
     * @param {boolean} bought
     */
    #setBoughtImage(bought) {
        this.#boughtTag.classList.remove('order-info-bought__icon--bought');
        this.#boughtTag.classList.remove('order-info-bought__icon--not-bought');

        if (bought) {
            this.#boughtTag.classList.add('order-info-bought__icon--bought');
            this.#boughtTag.title = this.#boughtTag.dataset.iconBoughtTitle;
        } else {
            this.#boughtTag.classList.add('order-info-bought__icon--not-bought');
            this.#boughtTag.title = this.#boughtTag.dataset.iconNotBoughtTitle;
        }
    }

    /**
     * @param {*} shop
     */
    #setShopInfo(shop) {
        if (shop.id === null) {
            this.#shopInfoTag.hidden = true;
        } else {
            this.#shopInfoTag.hidden = false;
        }

        /** @type {HTMLElement} */
        const shopDescriptionTag = document.getElementById('shop');
        shopDescriptionTag.classList.remove('show');
    }

    /**
     * @param {number} price
     * @param {number} amount
     *
     * @returns {number|null}
     */
    #calculatePriceTotal(price, amount) {
        if (price === null) {
            return null;
        }

        return price * amount;
    }


}