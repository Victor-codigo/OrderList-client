import { Controller } from '@hotwired/stimulus';
import * as form from 'App/modules/form';
import * as encodedUrlParameter from 'App/modules/EncodedUrlParameter';
import * as config from 'App/Config';

const LIST_ORDERS_NAME_PLACEHOLDER = '--list_orders_name--';

export default class extends Controller {
    /**
     * @type {HTMLInputElement}
     */
    #nameTag;

    /**
     * @type {HTMLTextAreaElement}
     */
    #descriptionTag;

    /**
     * @type {HTMLInputElement}
     */
    #dateToBuyTag;

    connect() {
        this.#nameTag = this.element.querySelector('[data-js-list-orders-name]');
        this.#descriptionTag = this.element.querySelector('[data-js-list-orders-description]');
        this.#dateToBuyTag = this.element.querySelector('[data-js-list-orders-date-to-buy]');
        this.#dateToBuyTag.min = new Date().toISOString().slice(0, -8);

        this.formValidate();
    }

    formValidate() {
        form.validate(this.element, null);
    }

    /**
     * @param {config.ItemData} listOrderData
     */
    setFormFieldValues(listOrderData) {
        this.#nameTag.value = listOrderData.name;
        this.#descriptionTag.value = listOrderData.description;
        this.#dateToBuyTag.value = listOrderData.dateToBuy;
        this.element.action = this.element.dataset.actionPlaceholder.replace(
            LIST_ORDERS_NAME_PLACEHOLDER,
            encodedUrlParameter.encodeUrlParameter(listOrderData.name)
        );
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {object} event.detail.content.itemData
     */
    handleMessageHomeListItemModify({ detail: { content } }) {
        this.setFormFieldValues(content.itemData);
    }
}

