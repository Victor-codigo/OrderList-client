import HomeSectionComponent from 'App/Twig/Components/HomeSection/Home/HomeSection_controller';
import * as locale from 'App/modules/Locale';

export default class extends HomeSectionComponent {

    /**
     * @type {HTMLSpanElement}
     */
    #priceBoughtTag;

    /**
     * @type {HTMLSpanElement}
     */
    #priceTotalTag;

    connect() {
        super.connect();

        this.#priceBoughtTag = this.element.querySelector('[data-js-price-bought]');
        this.#priceTotalTag = this.element.querySelector('[data-js-price-total]');

        const ordersData = this.#getOrdersPrices();
        const totalPrice = this.#calculateTotalPrice(ordersData);
        const boughtPrice = this.#calculateBoughtPrice(ordersData);
        this.#priceTotalTag.innerText = locale.formatToStringLocaleCurrency(totalPrice);
        this.#priceBoughtTag.innerText = locale.formatToStringLocaleCurrency(boughtPrice);
    }

    /**
     * @returns {{id: string, amount: number, bought: boolean, price: number}[]}
     */
    #getOrdersPrices() {
        const orders = Array.from(this.element.querySelectorAll('[data-controller="ListComponent"] li'));

        return orders.map((order) => {
            const orderData = JSON.parse(order.dataset.itemData);

            return {
                id: orderData.id,
                amount: orderData.amount,
                bought: orderData.bought,
                price: orderData.productShop.price === null ? 0 : orderData.productShop.price
            }
        });
    }


    /**
     * @param {{id: string, amount: number, bought: boolean, price: number}[]} ordersPrice
     *
     * @returns {number}
     */
    #calculateTotalPrice(ordersPrice) {
        return ordersPrice.reduce((total, order) => total + this.#calculateOrderPrice(order.price, order.amount), 0);
    }

    /**
     * @param {{id: string, amount: number, bought: boolean, price: number}[]} ordersPrice
     *
     * @returns {number}
     */
    #calculateBoughtPrice(ordersPrice) {
        return ordersPrice.reduce((total, order) => {
            if (!order.bought) {
                return total;
            }

            return total + this.#calculateOrderPrice(order.price, order.amount)
        },
            0
        );
    }

    /**
     * @param {number} amount
     * @param {number} price
     *
     * @return {number}
     */
    #calculateOrderPrice(amount, price) {
        return amount * price;
    }

    #updateListOrderBoughtPrice() {
        const ordersData = this.#getOrdersPrices();
        const boughtPrice = this.#calculateBoughtPrice(ordersData);

        this.#priceBoughtTag.innerText = locale.formatToStringLocaleCurrency(boughtPrice);
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {string} event.detail.content.id
     * @param {boolean} event.detail.content.bought
     */
    handleMessageOrderBoughtChanged({ detail: { content } }) {
        this.#updateListOrderBoughtPrice();
    }
}