import HomeSectionComponent from 'App/Twig/Components/HomeSection/Home/HomeSection_controller';
import * as locale from 'App/modules/Locale';
import * as endpoint from 'App/modules/ApiEndpoints';

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

        this.#updateListOrderBoughtPrice();
    }

    async #updateListOrderBoughtPrice() {
        const listOrdersId = this.element.dataset.listOrdersId;
        const groupId = this.element.dataset.groupId;

        try {
            const listOrdersPrice = await endpoint.getListOrdersPrice(listOrdersId, groupId);

            this.#priceTotalTag.textContent = locale.formatToStringLocaleCurrency(!isNaN(listOrdersPrice.total) ? listOrdersPrice.total : 0);
            this.#priceBoughtTag.textContent = locale.formatToStringLocaleCurrency(!isNaN(listOrdersPrice.bought) ? listOrdersPrice.bought : 0);
        } catch (Error) {
            this.#priceTotalTag.textContent = locale.formatToStringLocaleCurrency(0);
            this.#priceBoughtTag.textContent = locale.formatToStringLocaleCurrency(0);
        }
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