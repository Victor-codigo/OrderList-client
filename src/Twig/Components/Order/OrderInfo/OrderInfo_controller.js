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
    #shopAddressContainerTag;

    /**
     * @type {HTMLParagraphElement}
     */
    #shopAddressTag;

    /**
     * @type {HTMLParagraphElement}
     */
    #shopDescriptionTag;

    /**
     * @type {HTMLHRElement}
     */
    #shopAddressDescriptionSeparatorTag;

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
        this.#shopAddressContainerTag = this.element.querySelector('[data-js-item-shop-address-container]');
        this.#shopAddressTag = this.element.querySelector('[data-js-item-shop-address]');
        this.#shopDescriptionTag = this.element.querySelector('[data-js-item-shop-description]');
        this.#shopAddressDescriptionSeparatorTag = this.element.querySelector('[data-js-shop-description-address-separator]');
        this.#shopPriceTag = this.element.querySelector('[data-js-item-shop-price]');
        this.#priceTotalTag = this.element.querySelector('[data-js-item-shop-price-total]');
        this.#shopInfoTag = this.element.querySelector('[data-js-shop-info]');
    }

    /**
     * @param {ListOrdersData} data
     */
    setItemData(data) {
        this.titleTag.textContent = data.product.name;
        this.imageTag.src = data.image;
        this.setImage(data.image, data.noImage);
        this.dateTag.textContent = locale.formatDateToLocale(data.createdOn);
        this.descriptionTag.textContent = data.description;
        this.#setBoughtImage(data.bought);
        this.#amountTag.textContent = locale.formatAmountAndUnit(data.amount, data.productShop.unit);

        this.#productNameTag.textContent = data.product.name;
        this.#productDescriptionTag.textContent = data.product.description;

        this.#shopNameTag.textContent = data.shop.name;
        this.#shopDescriptionTag.textContent = data.shop.description;
        this.#setAddress(data.shop.address)
        this.#setShopAddressDescriptionSeparator(data.shop.address, data.shop.description);
        this.#shopPriceTag.textContent = locale.formatPriceCurrencyAndUnit(data.productShop.price, data.productShop.unit);

        this.#priceTotalTag.textContent = locale.formatToStringLocaleCurrency(this.#calculatePriceTotal(data.productShop.price, data.amount));
        this.#setShopInfo(data.shop);
    }

    /**
     * @param {string|null} address
     */
    #setAddress(address) {
        if (address === null || address === '') {
            this.#shopAddressContainerTag.hidden = true;
            return;
        }

        this.#shopAddressContainerTag.hidden = false;
        this.#shopAddressTag.textContent = address;
    }

    /**
     * @param {string|null} address
     * @param {string|null} description
     */
    #setShopAddressDescriptionSeparator(address, description) {
        if (address === null || address === '') {
            this.#shopAddressDescriptionSeparatorTag.hidden = true;

            return;
        }

        if (description === null || description === '') {
            this.#shopAddressDescriptionSeparatorTag.hidden = true;

            return;
        }

        this.#shopAddressDescriptionSeparatorTag.hidden = false;
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