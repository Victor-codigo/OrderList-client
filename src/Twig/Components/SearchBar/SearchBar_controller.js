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
        let parameters = {
            'group_id': this.element.dataset.groupId,
            'page': 1,
            'page_items': SEARCHBAR_AUTOCOMPLETE_MAX_RESULTS,
            'order_asc': true,
        };

        switch (url.getSection()) {
            case url.SECTIONS.SHOP:
                parameters['shop_name_filter_type'] = this.nameFilterTag.value;
                parameters['shop_name_filter_value'] = this.valueTag.value;

                return await apiEndpoints.getShopsNames(parameters);
            case url.SECTIONS.PRODUCT:
                if (this.sectionFilterTag.value === url.SECTIONS.SHOP) {
                    parameters['shop_name_filter_type'] = this.nameFilterTag.value;
                    parameters['shop_name_filter_value'] = this.valueTag.value;

                    return await apiEndpoints.getShopsNames(parameters);
                } else {
                    parameters['product_name_filter_type'] = this.nameFilterTag.value;
                    parameters['product_name_filter_value'] = this.valueTag.value;

                    return await apiEndpoints.getProductsNames(parameters);
                }
            case url.SECTIONS.ORDER:

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
