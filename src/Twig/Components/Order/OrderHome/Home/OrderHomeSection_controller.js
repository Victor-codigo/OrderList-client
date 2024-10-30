import HomeSectionComponent from 'App/Twig/Components/HomeSection/Home/HomeSection_controller';
import * as locale from 'App/modules/Locale';
import * as endpoint from 'App/modules/ApiEndpoints';
import * as communication from 'App/modules/ControllerCommunication';

export default class extends HomeSectionComponent {

    /**
     * @type {HTMLSpanElement}
     */
    #priceBoughtTag;

    /**
     * @type {HTMLSpanElement}
     */
    #priceTotalTag;

    /**
     * @type {string}
     */
    #listOrdersId;

    /**
     * @type {HTMLButtonElement}
     */
    #shareWhatsappButton;


    connect() {
        super.connect();

        this.#priceBoughtTag = this.element.querySelector('[data-js-price-bought]');
        this.#priceTotalTag = this.element.querySelector('[data-js-price-total]');
        this.#shareWhatsappButton = this.element.querySelector('[data-js-share-button]');
        this.#listOrdersId = this.element.dataset.listOrdersId;


        if (this.interactive) {
            this.#shareWhatsappButton.addEventListener('click', this.#shareWhatsAppCreate.bind(this));
            this.#updateListOrderBoughtPrice();
        }
    }

    disconnect() {
        this.#shareWhatsappButton.removeEventListener('click', this.#shareWhatsAppCreate);
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
     * @throws {Error}
     */
    async #shareWhatsAppCreate() {
        communication.sendMessageToChildController(this.#shareWhatsappButton, 'showButtonLoading');
        const responseData = await endpoint.createListOrdersShare(this.#listOrdersId);
        communication.sendMessageToChildController(this.#shareWhatsappButton, 'showButton');

        if (responseData.status !== 'ok') {
            throw new Error(responseData.message);
        }

        navigator.share({
            url: endpoint.GET_SHARE_LIST_ORDERS_URL.replace('{list_orders_id}', responseData.data.list_orders_id),
            title: this.element.dataset.listName
        });
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {string} event.detail.content.id
     * @param {boolean} event.detail.content.bought
     */
    handleMessageOrderBoughtChanged({ detail: { content } }) {
        if (this.interactive) {
            this.#updateListOrderBoughtPrice();
        }
    }
}