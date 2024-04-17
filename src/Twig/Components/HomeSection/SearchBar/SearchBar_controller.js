import { Controller } from '@hotwired/stimulus';
import * as apiEndpoints from 'App/modules/ApiEndpoints';
import * as url from 'App/modules/Url';


const SEARCHBAR_AUTOCOMPLETE_MAX_RESULTS = 50;


export default class extends Controller {
    /**
     * @type {number|undefined}
     */
    searchTimeoutId;

    /**
     * @type {HTMLFormElement}
     */
    searchBarFormTag;

    /**
     * @type {HTMLInputElement}
     */
    valueTag;

    /**
     * @type {HTMLInputElement}
     */
    sectionFilterTag;

    /**
     * @type {HTMLInputElement}
     */
    nameFilterTag;

    /**
     * @type {HTMLDataListElement}
     */
    searchDataListTag;

    /**
     * @type {function}
     */
    getDataFromApiCallback;

    connect() {
        this.searchTimeoutId = null;
        this.searchBarFormTag = this.element.querySelector('[data-js-searchbar-form]');
        this.valueTag = this.element.querySelector('[data-js-value]');
        this.sectionFilterTag = this.element.querySelector('[data-js-section-filter]');
        this.nameFilterTag = this.element.querySelector('[data-js-name-filter]');
        this.searchDataListTag = this.element.querySelector('#search-data-list');

        this.searchBarFormTag.addEventListener('submit', this.#onSubmitHandler.bind(this));
        this.valueTag.addEventListener('input', this.#onSearchValueInputHandler.bind(this));
    }

    disconnect() {
        this.searchBarFormTag.removeEventListener('submit', this.#onSubmitHandler);
        this.valueTag.removeEventListener('input', this.#onSearchValueInputHandler);
    }

    /**
     * @param {number} delayInMs
     * @param {function} callback
     * @param  {...any} args
     */
    #setTimeout(delayInMs, callback, ...args) {
        clearTimeout(this.searchTimeoutId);

        this.searchTimeoutId = setTimeout(() => callback(...args), delayInMs);
    }

    async #getAutoCompleteData() {
        if (this.valueTag.value === '') {
            this.#updateSearchDatalist([]);

            return;
        }

        const shopsNames = await this.#getDataFromApi();
        this.#updateSearchDatalist(shopsNames);
    }

    #updateSearchDatalist(data) {
        this.searchDataListTag
            .querySelectorAll('option')
            .forEach((option) => option.remove());

        const searchDataListOptions = data.map((item) => {
            const option = document.createElement('option');
            option.value = item;

            return option;
        });

        searchDataListOptions.forEach((item) => this.searchDataListTag.appendChild(item));
    }

    async #getDataFromApi() {
        switch (url.getSection().replace('-', '_')) {
            case url.SECTIONS.SHOP:
                return await this.#getShopsNames(this.nameFilterTag.value, this.valueTag.value);
            case url.SECTIONS.PRODUCT:
                return await this.#getDataFromApiSectionProduct();
            case url.SECTIONS.LIST_ORDERS:
                return await this.#getDataFromApiSectionListOrders();
            default:
                return await this.#getDataFromApiSectionListOrders();

        }
    }

    /**
     * @returns {Promise<string[]>}
     */
    async #getDataFromApiSectionProduct() {
        if (this.sectionFilterTag.value === url.SECTIONS.SHOP) {
            return await this.#getShopsNames(this.nameFilterTag.value, this.valueTag.value);
        }

        return await this.#getProductsNames(this.nameFilterTag.value, this.valueTag.value);
    }

    /**
     * @returns {Promise<string[]>}
     */
    async #getDataFromApiSectionListOrders() {
        console.log(url.getSubSection().replace('-', '_'));
        if (url.getSubSection().replace('-', '_') === url.SECTIONS.ORDERS) {
            return await this.#getDataFromApiSubSectionOrders();
        }

        if (this.sectionFilterTag.value === url.SECTIONS.SHOP) {
            return await this.#getShopsNames(this.nameFilterTag.value, this.valueTag.value);
        } else if (this.sectionFilterTag.value === url.SECTIONS.PRODUCT) {
            return await this.#getProductsNames(this.nameFilterTag.value, this.valueTag.value);
        }

        return await this.#getListOrdersNames(this.nameFilterTag.value, this.sectionFilterTag.value, this.valueTag.value);
    }

    /**
     * @returns {Promise<string[]>}
     */
    async #getDataFromApiSubSectionOrders() {
        if (this.sectionFilterTag.value === url.SECTIONS.SHOP) {
            return await this.#getShopsNames(this.nameFilterTag.value, this.valueTag.value);
        } else if (this.sectionFilterTag.value === url.SECTIONS.PRODUCT
            || this.sectionFilterTag.value === url.SECTIONS.ORDER) {
            return await this.#getProductsNames(this.nameFilterTag.value, this.valueTag.value);
        }
    }

    #getParametersDefault() {
        return {
            groupId: this.element.dataset.groupId,
            page: 1,
            pageItems: SEARCHBAR_AUTOCOMPLETE_MAX_RESULTS,
            orderAsc: true,
        };
    }

    /**
     * @param {string} nameFilter
     * @param {string} valueFilter
     * @returns {Promise<string[]>}
     */
    #getShopsNames(nameFilter, valueFilter) {
        let parameters = this.#getParametersDefault();

        return apiEndpoints.getShopsNames(
            parameters.groupId,
            parameters.page,
            parameters.pageItems,
            null,
            null,
            null,
            nameFilter,
            valueFilter,
            parameters.orderAsc
        );
    }

    /**
     * @param {string} nameFilter
     * @param {string} valueFilter
     * @returns {Promise<string[]>}
     */
    #getProductsNames(nameFilter, valueFilter) {
        let parameters = this.#getParametersDefault();

        return apiEndpoints.getProductsNames(
            parameters.groupId,
            parameters.page,
            parameters.pageItems,
            null,
            null,
            null,
            nameFilter,
            valueFilter,
            null,
            null,
            parameters.orderAsc
        );
    }

    /**
     * @param {string} nameFilter
     * @param {string} sectionFilter
     * @param {string} valueFilter
     * @returns {Promise<string[]>}
     */
    async #getListOrdersNames(nameFilter, sectionFilter, valueFilter) {
        let parameters = this.#getParametersDefault();

        try {
            return await apiEndpoints.getListOrdersNames(
                parameters.groupId,
                parameters.page,
                parameters.pageItems,
                null,
                null,
                sectionFilter,
                nameFilter,
                valueFilter,
                parameters.orderAsc
            );
        } catch (error) {
            return new Promise((resolve) => []);
        }
    }

    #onSearchValueInputHandler() {
        this.#setTimeout(300, this.#getAutoCompleteData.bind(this));
    }

    #onSubmitHandler() {
        if (this.valueTag.value == '') {
            this.nameFilterTag.removeAttribute('name');
        }
    }
}
