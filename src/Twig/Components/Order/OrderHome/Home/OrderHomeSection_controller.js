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

            this.#priceTotalTag.innerText = locale.formatToStringLocaleCurrency(listOrdersPrice.total);
            this.#priceBoughtTag.innerText = locale.formatToStringLocaleCurrency(listOrdersPrice.bought);
        } catch (Error) {
            this.#priceTotalTag.innerText = locale.formatToStringLocaleCurrency(0);
            this.#priceBoughtTag.innerText = locale.formatToStringLocaleCurrency(0);
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