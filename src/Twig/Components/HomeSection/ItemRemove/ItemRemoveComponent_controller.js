import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    /**
     * @type {HTMLParagraphElement}
     */
    messageTag;
    /**
     * @type {string}
     */
    messagePlaceholder;
    /**
     * @type {string}
     */
    formRemoveItemIdFieldName;

    connect() {
        this.messageTag = this.element.querySelector('[data-js-message]');
        this.messagePlaceholder = this.messageTag.dataset.placeholder;
        this.formRemoveItemIdFieldName = `${this.element.name}[items_id][]`;
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     */
    handleMessageRemoveListItem({ detail: { content } }) {
        if (this.element.hasAttribute('data-remove-multi')) return;

        this.loadComponentData(content.items);
    }

    handleMessageHomeSectionRemoveMulti({ detail: { content } }) {
        if (!this.element.hasAttribute('data-remove-multi')) return;

        this.loadComponentData(content.items);
    }

    /**
     * @param {object} items
     */
    loadComponentData(items) {
        let itemsNames = [];

        this.clearInputItemIds();
        items.forEach((item) => {
            this.createInputItemId(item.id);

            itemsNames.push(item.name);
        });

        this.changePlaceholderItemName(itemsNames);
    }

    clearInputItemIds() {
        const inputItemIds = this.element.querySelectorAll(`input[type="hidden"][name="${this.formRemoveItemIdFieldName}"]`);

        inputItemIds.forEach((inputItemId) => this.element.removeChild(inputItemId));
    }

    /**
     * @param {string[]} itemsNames
     */
    changePlaceholderItemName(itemsNames) {
        const listItems = itemsNames.map((itemName) => `<li class="list-group-item  text-start  fw-bold  align-self-center  text-center  w-100">${itemName}</li>`);
        const list = `<ul class="list-group  list-group-flush  d-flex  flex-column">${listItems.join('')}</ul>`;
        let message = `<p>${this.messagePlaceholder.replace('{item_placeholder}', '</p>{item_placeholder}<p>')}`;

        message = `<p>${message.replace('{item_placeholder}', list)}</p>`;
        this.messageTag.innerHTML = message;
    }

    /**
     * @param {string} itemId
     */
    createInputItemId(itemId) {
        const inputItemId = document.createElement('input');

        inputItemId.type = 'hidden';
        inputItemId.name = this.formRemoveItemIdFieldName;
        inputItemId.value = itemId;

        this.element.appendChild(inputItemId);
    }
}