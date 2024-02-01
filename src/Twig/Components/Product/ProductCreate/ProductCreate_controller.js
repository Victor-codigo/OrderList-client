import { Controller } from '@hotwired/stimulus';
import * as form from 'App/modules/form';
import * as event from 'App/modules/Event';
import * as communication from 'App/modules/ControllerCommunication';


export default class extends Controller {
    connect() {
        this.itemPriceGroupCurrent = null;
        this.itemPriceAddComponentTag = this.element.querySelector('[data-controller="ItemPriceAddComponent"]');
        this.dropzoneComponentTag = this.element.querySelector('[data-controller="DropZoneComponent"]');

        this.formValidate();

        event.addEventListenerDelegate({
            element: this.element,
            elementDelegateSelector: '[data-js-item-group]',
            eventName: 'click',
            callbackListener: this.#setPriceGroupCurrent.bind(this),
            eventOptions: {}
        });
    }

    disconnect() {
        event.removeEventListenerDelegate(this.element, 'click', this.#setPriceGroupCurrent);
    }

    /**
     * @param {HTMLElement} itemGroupTag
     * @param {Event} event
     */
    #setPriceGroupCurrent(itemGroupTag, event) {
        this.itemPriceGroupCurrent = itemGroupTag;
    }

    formValidate() {
        form.validate(this.element, null);
    }

    #clearForm() {
        this.element.reset();
        this.element.classList.remove('was-validated');
        this.#sendMessageDropzoneClear();
        this.#sendMessageItemPriceAddClear();
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {string} event.detail.content.shopId
     * @param {string} event.detail.content.shopName
     */
    handleMessageShopSelected({ detail: { content } }) {
        const itemPriceId = this.itemPriceGroupCurrent.querySelector('[data-js-item-id]');
        const itemPriceName = this.itemPriceGroupCurrent.querySelector('[data-js-item-name]');

        itemPriceId.value = content.shopId;
        itemPriceName.value = content.shopName;
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {string} event.detail.content.id
     * @param {string} event.detail.content.name
     * @param {string} event.detail.content.itemsAdded
     */
    handleMessageItemPriceSelected({ detail: { content } }) {
        this.#sendMessageItemPriceSelectedToShopsListAjaxComponent(content.itemsAdded);
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {boolean} event.detail.content.showedFirstTime
     * @param {HTMLElement} event.detail.content.triggerElement
     * @param {object} event.detail.content.triggerElementData
     * @param {string} event.detail.content.triggerElementData.modalBefore
     * @param {string} event.detail.content.triggerElementData.controllerModalEventHandler
     */
    handleMessageBeforeShowed({ detail: { content } }) {
        if (typeof content.triggerElement === 'undefined') {
            return;
        }

        if (!content.triggerElement.hasAttribute('data-js-add-item')) {
            return;
        }

        this.#clearForm();
    }

    /**
     * @param {object} shopAdded
     * @param {string} shopAdded.id
     * @param {string} shopAdded.name
     * @param {string} shopAdded.price
     */
    #sendMessageItemPriceSelectedToShopsListAjaxComponent(shopsAdded) {
        communication.sendMessageToNotRelatedController(this.element, 'itemPriceSelected', {
            shopsAdded: shopsAdded
        },
            'ShopsListAjaxComponent'
        );
    }

    #sendMessageItemPriceAddClear() {
        communication.sendMessageToChildController(this.itemPriceAddComponentTag, 'clear');
    }

    #sendMessageDropzoneClear() {
        communication.sendMessageToChildController(this.dropzoneComponentTag, 'clear')
    }
}
