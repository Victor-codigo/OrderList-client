import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.messageTag = this.element.querySelector('[data-js-message]');
        this.messagePlaceholder = this.messageTag.dataset.placeholder;
        this.shopsIdTag = this.element.querySelector('[data-js-shops-id]');
        this.componentId = this.element.id;
        this.formRemoveShopIdFieldName = `${this.element.name}[shops_id][]`;
    }

    handleOnShopRemoveEvent({ detail: { content } }) {
        if (this.element.hasAttribute('data-remove-multi')) return;

        this.#loadComponentData(content.items);
    }

    handleOnShopRemoveMultiEvent({ detail: { content } }) {
        if (!this.element.hasAttribute('data-remove-multi')) return;

        this.#loadComponentData(content.items);
    }

    #loadComponentData(shops) {
        let shopNames = [];

        this.#cleanInputShopIds();
        shops.forEach((shop) => {
            this.#createInputShopId(shop.id);

            shopNames.push(shop.name);
        });

        this.#changePlaceholderShopName(shopNames.join(', '));
    }

    #cleanInputShopIds() {
        const inputShopIds = this.element.querySelectorAll(`input[type="hidden"][name="${this.formRemoveShopIdFieldName}"]`);

        inputShopIds.forEach((inputShopId) => this.element.removeChild(inputShopId));
    }

    #changePlaceholderShopName(shopName) {
        this.messageTag.innerHTML = this.messagePlaceholder.replace('{shop_placeholder}', `<strong>${shopName}</strong>`);
    }

    #createInputShopId(shopId) {
        const inputShopId = document.createElement('input');

        inputShopId.type = 'hidden';
        inputShopId.name = this.formRemoveShopIdFieldName;
        inputShopId.value = shopId;

        this.element.appendChild(inputShopId);
    }
}