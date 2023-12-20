import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.messageTag = this.element.querySelector('[data-js-message]');
        this.messagePlaceholder = this.messageTag.dataset.placeholder;
        this.productsIdTag = this.element.querySelector('[data-js-products-id]');
        this.componentId = this.element.id;
        this.formRemoveProductIdFieldName = `${this.element.name}[products_id][]`;
    }

    handleOnProductRemoveEvent({ detail: { content } }) {
        if (this.element.hasAttribute('data-remove-multi')) return;

        this.#loadComponentData(content.items);
    }

    handleOnProductRemoveMultiEvent({ detail: { content } }) {
        if (!this.element.hasAttribute('data-remove-multi')) return;

        this.#loadComponentData(content.items);
    }

    #loadComponentData(products) {
        let productNames = [];

        this.#cleanInputProductIds();
        products.forEach((product) => {
            this.#createInputProductId(product.id);

            productNames.push(product.name);
        });

        this.#changePlaceholderProductName(productNames.join(', '));
    }

    #cleanInputProductIds() {
        const inputProductIds = this.element.querySelectorAll(`input[type="hidden"][name="${this.formRemoveProductIdFieldName}"]`);

        inputProductIds.forEach((inputProductId) => this.element.removeChild(inputProductId));
    }

    #changePlaceholderProductName(productName) {
        this.messageTag.innerHTML = this.messagePlaceholder.replace('{product_placeholder}', `<strong>${productName}</strong>`);
    }

    #createInputProductId(productId) {
        const inputProductId = document.createElement('input');

        inputProductId.type = 'hidden';
        inputProductId.name = this.formRemoveProductIdFieldName;
        inputProductId.value = productId;

        this.element.appendChild(inputProductId);
    }
}