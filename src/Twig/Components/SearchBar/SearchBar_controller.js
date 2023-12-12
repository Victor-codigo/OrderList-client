import { Controller } from '@hotwired/stimulus';


const SEARCHBAR_AUTOCOMPLETE_MAX_RESULTS = 50;

export default class extends Controller {

    connect() {
        this.searchTimeoutId = null;
        this.searchBarFormTag = this.element.querySelector('[data-js-searchbar-form]');
        this.valueTag = this.element.querySelector('[data-js-value]');
        this.filterTag = this.element.querySelector('[data-js-filter]');
        this.searchDataListTag = this.element.querySelector('#search-data-list');

        this.searchBarFormTag.addEventListener('submit', this.#onSubmitHandler.bind(this));
        this.searchBarFormTag.addEventListener('input', this.#onSearchValueInputHandler.bind(this));
    }

    disconnect() {
        this.searchBarFormTag.removeEventListener('submit', this.#onSubmitHandler);
        this.searchBarFormTag.removeEventListener('input', this.#onSearchValueInputHandler);
    }

    #onSearchValueInputHandler() {
        this.#setTimeout(300, this.#getAutoCompleteData.bind(this));
    }

    #setTimeout(delayInMs, callback, ...args) {
        clearTimeout(this.searchTimeoutId);

        this.searchTimeoutId = setTimeout(() => callback(...args), delayInMs);
    }

    #onSubmitHandler() {
        if (this.valueTag.value == '') {
            this.filterTag.removeAttribute('name');
        }
    }

    async #getAutoCompleteData() {
        const parameters = {
            'group_id': this.element.dataset.groupId,
            'page': 1,
            'page_items': SEARCHBAR_AUTOCOMPLETE_MAX_RESULTS,
            'order_asc': true,
            'shop_name_filter_type': this.filterTag.value,
            'shop_name_filter_value': this.valueTag.value,
        }
        const queryParameters = Object.entries(parameters)
            .map(([name, value]) => `${name}=${value}`)
            .join('&');

        try {
            const response = await fetch(`${this.element.dataset.urlSearchAutocomplete}?${queryParameters}`);

            if (response.status === 200) {
                const data = await response.json();
                this.#updateSearchDatalist(data['search_value']);
            }
        }
        catch (error) {
            console.log('Error getting autocomplete data', error);
        }

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

}
