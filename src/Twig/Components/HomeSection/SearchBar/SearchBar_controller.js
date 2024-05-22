import { Controller } from '@hotwired/stimulus';
import * as apiEndpoints from 'App/modules/ApiEndpoints';
import * as url from 'App/modules/Url';
import Autocomplete from "App/Dependencies/bootstrap5-autocomplete/autocomplete.min";


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
     * @type {function}
     */
    getDataFromApiCallback;

    connect() {
        this.searchTimeoutId = null;
        this.searchBarFormTag = this.element.querySelector('[data-js-searchbar-form]');
        this.valueTag = this.element.querySelector('[data-js-value]');
        this.sectionFilterTag = this.element.querySelector('[data-js-section-filter]');
        this.nameFilterTag = this.element.querySelector('[data-js-name-filter]');

        this.#createAutocomplete();

        this.searchBarFormTag.addEventListener('submit', this.#onSubmitHandler.bind(this));
    }

    disconnect() {
        this.searchBarFormTag.removeEventListener('submit', this.#onSubmitHandler);
    }

    #createAutocomplete() {
        Autocomplete.init('[data-js-value]', {
            source: this.#setTimeout(async (input, callback) => await callback(await this.#getDataFromApi()), 500),
            fullWidth: true,
            fixed: true,
            preventBrowserAutocomplete: true,
        });
    }

    /**
     * @param {Function} callback
     * @param {number} timeout
     * @returns {Function}
     */
    #setTimeout(callback, timeout = 300) {
        let timer;

        return (...args) => {
            clearTimeout(timer);
            timer = setTimeout(() => callback.apply(this, args), timeout);
        };
    }

    async #getDataFromApi() {
        switch (url.getSection().replace('-', '_')) {
            case url.SECTIONS.SHOP:
                return await this.#getShopsNames(this.nameFilterTag.value, this.valueTag.value);
            case url.SECTIONS.PRODUCT:
                return await this.#getDataFromApiSectionProduct();
            case url.SECTIONS.LIST_ORDERS:
                return await this.#getDataFromApiSectionListOrders();
            case url.SECTIONS.GROUP:
                return await this.#getDataFromApiSectionGroup();
            case url.SECTIONS.GROUP_USERS:
                return await this.#getDataFromApiSectionGroupUsers();
            default:
                return await this.#getDataFromApiSectionListOrders();

        }
    }

    /**
     * @returns {Promise<string[]>}
     */
    async #getDataFromApiSectionGroup() {
        return await this.#getGroupsNames(this.nameFilterTag.value, this.sectionFilterTag.value, this.valueTag.value);
    }

    /**
     * @returns {Promise<string[]>}
     */
    async #getDataFromApiSectionGroupUsers() {
        return await this.#getGroupsUsersNames(this.element.dataset.groupId, this.nameFilterTag.value, this.sectionFilterTag.value, this.valueTag.value);
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
        if (url.getSection() === url.SECTIONS.ORDERS) {
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

    /**
     * @param {string} nameFilter
     * @param {string} sectionFilter
     * @param {string} valueFilter
     * @returns {Promise<string[]>}
     */
    async #getGroupsNames(nameFilter, sectionFilter, valueFilter) {
        let parameters = this.#getParametersDefault();

        try {
            return await apiEndpoints.getGroupsNames(
                parameters.page,
                parameters.pageItems,
                sectionFilter,
                nameFilter,
                valueFilter,
                parameters.orderAsc
            );
        } catch (error) {
            return new Promise((resolve) => []);
        }
    }

    /**
     * @param {string} groupId
     * @param {string} nameFilter
     * @param {string} sectionFilter
     * @param {string} valueFilter
     * @returns {Promise<string[]>}
     */
    async #getGroupsUsersNames(groupId, nameFilter, sectionFilter, valueFilter) {
        let parameters = this.#getParametersDefault();

        try {
            return await apiEndpoints.getGroupUsersNames(
                groupId,
                parameters.page,
                parameters.pageItems,
                sectionFilter,
                nameFilter,
                valueFilter,
                parameters.orderAsc
            );
        } catch (error) {
            return new Promise((resolve) => []);
        }
    }

    #onSubmitHandler() {
        if (this.valueTag.value == '') {
            this.nameFilterTag.removeAttribute('name');
        }
    }
}
