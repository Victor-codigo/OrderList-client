import HomeSectionComponent from 'App/Twig/Components/HomeSection/Home/HomeSection_controller';
import * as locale from 'App/modules/Locale';
import * as endpoint from 'App/modules/ApiEndpoints';
import * as communication from 'App/modules/ControllerCommunication';
import * as bootstrap from 'bootstrap';

const SHARED_RESOURCE_PLACEHOLDER = '--shared_recourse_id--';
const LOGO_URL = location.protocol + '//' + location.host + '/build/images/common/logo-white.png';

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
    #shareButton;

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
        this.#shareButton = this.element.querySelector('[data-js-share-button]');
        this.#listOrdersId = this.element.dataset.listOrdersId;
        this.#guestUserRestrictionInfoModal = new bootstrap.Modal('#info_guest_user_restriction_modal');
        this.#shareBrowserNotCompatibleInfoModal = new bootstrap.Modal('#info_share_browser_not_compatible_modal');


        if (this.interactive) {
            this.#shareButton.addEventListener('click', this.#shareCreate.bind(this));
            this.#updateListOrderBoughtPrice();
        }
    }

    disconnect() {
        this.#shareButton.removeEventListener('click', this.#shareCreate);
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
    async #shareCreate() {
        if (!navigator.share) {
            this.#shareBrowserNotCompatibleInfoModal.show();

            return;
        }

        communication.sendMessageToChildController(this.#shareButton, 'showButtonLoading');
        const responseData = await endpoint.createListOrdersShare(this.#listOrdersId);
        communication.sendMessageToChildController(this.#shareButton, 'showButton');

        if (responseData.status === 'ok') {
            await this.#share(responseData.data.list_orders_id);

            return;
        }

        if (Object.hasOwn(responseData.errors, 'tryout_route_permissions')) {
            this.#guestUserRestrictionInfoModal.show();

            return;
        }

        throw new Error(responseData.message);
    }

    /**
     * @param {string} sharedRecourseId
     *
     * @returns {Promise<void>}
     * @throws {Error}
     */
    async #share(sharedRecourseId) {
        try {
            let shareIconResponse = await fetch(LOGO_URL);
            let shareIcon = new File(
                [await shareIconResponse.blob()],
                'logo.png',
                {
                    type: 'image/png',
                    lastModified: new Date().getTime()
                }
            );

            navigator.share({
                title: this.element.dataset.listName,
                text: location.hostname,
                url: this.element.dataset.shareUrl.replace(SHARED_RESOURCE_PLACEHOLDER, sharedRecourseId),
                files: [shareIcon]
            });

            return;
        } catch (Error) {
            throw new Error(Error.message);
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
        if (this.interactive) {
            this.#updateListOrderBoughtPrice();
        }
    }
}