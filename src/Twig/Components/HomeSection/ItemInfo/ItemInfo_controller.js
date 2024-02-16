import { Controller } from '@hotwired/stimulus';
import * as html from 'App/modules/Html';
import * as url from 'App/modules/Url';
import * as config from 'App/Config';
import * as unitMeasure from 'App/modules/UnitMeasure';

export default class extends Controller {
    /**
     * @type {HTMLHeadElement}
     */
    #titleTag;

    /**
     * @type {HTMLSpanElement}
     */
    #dateTag;

    /**
     * @type {HTMLParagraphElement}
     */
    #descriptionTag;

    /**
     * @type {HTMLImageElement}
     */
    #imageTag;

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
     * @param {string} date
     */
    #getDataLocale(date) {
        const locale = url.getLocale();
        let itlLocale = 'es-ES';

        if (locale === 'en') {
            itlLocale = 'en-US';
        }

        return new Date(date).toLocaleDateString(itlLocale, config.dateFormat);
    }

    /**
     * @param {config.ItemData} data
     */
    #setItemData(data) {
        this.#titleTag.innerHTML = html.escape(data.name);
        this.#descriptionTag.innerHTML = html.escape(data.description === null ? '' : data.description);
        this.#dateTag.innerHTML = html.escape(this.#getDataLocale(data.createdOn));
        this.#imageTag.src = html.escape(data.image);
        this.#setPrices(data.shops);
    }

    /**
     * @param {config.ItemShopData[]} itemPrices
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

            numberTableCell.innerHTML = html.escape(rowCounter.toString());
            nameTableCell.innerHTML = html.escape(itemPrice.name);
            priceTableCell.innerHTML = html.escape(itemPrice.price === null ? '' : `${itemPrice.price.toString()} ${config.CURRENCY}/${unitMeasure.parseApiUnits(itemPrice.unit)}`);

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
        emptyTableCell.innerHTML = html.escape(this.#itemsPriceTag.dataset.emptyMessage);

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
        this.#setItemData(content.itemData);
    }
}