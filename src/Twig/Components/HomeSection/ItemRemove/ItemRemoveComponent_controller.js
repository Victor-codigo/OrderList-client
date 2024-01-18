import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        this.messageTag = this.element.querySelector('[data-js-message]');
        this.messagePlaceholder = this.messageTag.dataset.placeholder;
        this.itemsIdTag = this.element.querySelector('[data-js-items-id]');
        this.componentId = this.element.id;
        this.formRemoveItemIdFieldName = `${this.element.name}[items_id][]`;
    }

    handleMessageRemoveListItem({ detail: { content } }) {
        if (this.element.hasAttribute('data-remove-multi')) return;

        this.#loadComponentData(content.items);
    }

    handleMessageHomeSectionRemoveMulti({ detail: { content } }) {
        if (!this.element.hasAttribute('data-remove-multi')) return;

        this.#loadComponentData(content.items);
    }

    #loadComponentData(items) {
        let itemsNames = [];

        this.#cleanInputItemIds();
        items.forEach((item) => {
            this.#createInputItemId(item.id);

            itemsNames.push(item.name);
        });

        this.#changePlaceholderItemName(itemsNames);
    }

    #cleanInputItemIds() {
        const inputItemIds = this.element.querySelectorAll(`input[type="hidden"][name="${this.formRemoveItemIdFieldName}"]`);

        inputItemIds.forEach((inputItemId) => this.element.removeChild(inputItemId));
    }

    #changePlaceholderItemName(itemsNames) {
        const listItems = itemsNames.map((itemName) => `<li class="list-group-item  text-start  fw-bold  align-self-center  text-center  w-100">${itemName}</li>`);
        const list = `<ul class="list-group  list-group-flush  d-flex  flex-column">${listItems.join('')}</ul>`;
        let message = `<p>${this.messagePlaceholder.replace('{item_placeholder}', '</p>{item_placeholder}<p>')}`;

        message = `<p>${message.replace('{item_placeholder}', list)}</p>`;
        this.messageTag.innerHTML = message;
    }

    #createInputItemId(itemId) {
        const inputItemId = document.createElement('input');

        inputItemId.type = 'hidden';
        inputItemId.name = this.formRemoveItemIdFieldName;
        inputItemId.value = itemId;

        this.element.appendChild(inputItemId);
    }
}