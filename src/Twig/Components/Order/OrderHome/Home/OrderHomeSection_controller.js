import HomeSectionComponent from 'App/Twig/Components/HomeSection/Home/HomeSection_controller';
import * as locale from 'App/modules/Locale';
import * as endpoint from 'App/modules/ApiEndpoints';
import * as communication from 'App/modules/ControllerCommunication';
import * as bootstrap from 'bootstrap';

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

    /**
     * @type {bootstrap.Modal}}
     */
    #guestUserRestrictionInfoModal;

    /**
     * @type {bootstrap.Modal}}
     */
    #shareBrowserNotCompatibleInfoModal;

    connect() {
        super.connect();

        this.#priceBoughtTag = this.element.querySelector('[data-js-price-bought]');
        this.#priceTotalTag = this.element.querySelector('[data-js-price-total]');
        this.#shareWhatsappButton = this.element.querySelector('[data-js-share-button]');
        this.#listOrdersId = this.element.dataset.listOrdersId;
        this.#guestUserRestrictionInfoModal = new bootstrap.Modal('#info_guest_user_restriction_modal');
        this.#shareBrowserNotCompatibleInfoModal = new bootstrap.Modal('#info_share_browser_not_compatible_modal');


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
        if (!navigator.share) {
            this.#shareBrowserNotCompatibleInfoModal.show();

            return;
        }

        communication.sendMessageToChildController(this.#shareWhatsappButton, 'showButtonLoading');
        const responseData = await endpoint.createListOrdersShare(this.#listOrdersId);
        communication.sendMessageToChildController(this.#shareWhatsappButton, 'showButton');

        if (responseData.status === 'ok') {
            navigator.share({
                url: this.element.dataset.shareUrl.replace('--shared_recourse_id--', responseData.data.list_orders_id),
                text: this.element.dataset.listName
            });

            return;
        }

        if (Object.hasOwn(responseData.errors, 'tryout_route_permissions')) {
            this.#guestUserRestrictionInfoModal.show();

            return;
        }

        throw new Error(responseData.message);
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