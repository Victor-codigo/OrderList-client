import { Controller } from '@hotwired/stimulus';
import * as config from 'App/Config';
import * as locale from 'App/modules/Locale';

export default class extends Controller {
    /**
     * @type {HTMLHeadElement}
     */
    #titleTag;
    get titleTag() { return this.#titleTag };

    /**
     * @type {HTMLSpanElement}
     */
    #dateTag;
    get dateTag() { return this.#dateTag };

    /**
     * @type {HTMLParagraphElement}
     */
    #descriptionTag;
    get descriptionTag() { return this.#descriptionTag };

    /**
     * @type {HTMLImageElement}
     */
    #imageTag;
    get imageTag() { return this.#imageTag };

    /**
     * @type {HTMLTableSectionElement}
     */
    #itemsPriceTag;

    connect() {
        this.#titleTag = this.element.querySelector('[data-js-item-title]');
        this.#dateTag = this.element.querySelector('[data-js-item-date]');
        this.#descriptionTag = this.element.querySelector('[data-js-item-description]');
        this.#imageTag = this.element.querySelector('[data-js-item-image]');
        this.#itemsPriceTag = this.element.querySelector('[data-js-item-prices]');
    }

    /**
     * @param {config.ItemData} data
     */
    setItemData(data) {
        this.#titleTag.textContent = data.name;
        this.#descriptionTag.textContent = data.description === null ? '' : data.description;
        this.#dateTag.textContent = locale.formatDateToLocale(data.createdOn);
        this.setImage(data.image, data.noImage);

        if (typeof data.itemsPrices !== 'undefined') {
            this.#setPrices(data.itemsPrices);
        }
    }

    /**
     * @param {string} image
     * @param {boolean} noImage
     */
    setImage(image, noImage) {
        this.#imageTag.src = image;

        if (noImage) {
            this.#imageTag.classList.add('item-info__image--svg');
        } else {
            this.#imageTag.classList.remove('item-info__image--svg');
        }

    }

    /**
     * @param {config.ItemPriceData[]} itemPrices
     */
    #setPrices(itemPrices) {
        let rowCounter = 1;

        if (itemPrices.length === 0) {
            this.#setNoPrices();

            return;
        }

        this.#itemsPriceTag.innerHTML = '';

        itemPrices.forEach((itemPrice) => {
            const tableRow = document.createElement('tr');
            const numberTableCell = document.createElement('th');
            const nameTableCell = document.createElement('td');
            const priceTableCell = document.createElement('td');
            numberTableCell.classList.add('ps-5');
            priceTableCell.classList.add('pe-5');

            numberTableCell.textContent = rowCounter.toString();
            nameTableCell.textContent = itemPrice.name;
            priceTableCell.textContent = itemPrice.price === null ? '' : locale.formatPriceCurrencyAndUnit(itemPrice.price, itemPrice.unit);

            tableRow.replaceChildren(numberTableCell, nameTableCell, priceTableCell);
            this.#itemsPriceTag.appendChild(tableRow);
            rowCounter++;
        });
    }

    #setNoPrices() {
        this.#itemsPriceTag.innerHTML = '';

        const tableRow = document.createElement('tr');
        const emptyTableCell = document.createElement('td');

        emptyTableCell.colSpan = 3;
        emptyTableCell.classList.add('pt-4', 'pb-4', 'text-center', 'border-0');
        emptyTableCell.textContent = this.#itemsPriceTag.dataset.emptyMessage;

        tableRow.appendChild(emptyTableCell);
        this.#itemsPriceTag.appendChild(tableRow);
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {config.ItemData} event.detail.content.itemData
     */
    handleMessageHomeListItemInfo({ detail: { content } }) {
        this.setItemData(content.itemData);
    }
}