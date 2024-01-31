import { Controller } from '@hotwired/stimulus';
import * as endpoint from 'App/modules/ApiEndpoints';

export default class extends Controller {
    connect() {
        this.endpointName = this.element.dataset.endpointName;
        this.endpointResponseIndexName = this.element.dataset.endpointResponseIndexName;
        this.containerTag = this.element.querySelector('[data-js-container]');
        this.placeholderTemplate = this.element.querySelector('[data-js-placeholder-template]');
        this.endpointQueryParameters = this.#getEndpointQueryParams();
    }

    /**
     * @returns {Object}
     */
    #getEndpointQueryParams() {
        return JSON.parse(this.element.dataset.endpointQueryParams);
    }

    #showPlaceholder() {
        this.#setContent(this.placeholderTemplate.content.cloneNode(true));
    }

    /**
     * @callback responseManageCallback
     * @param {Object} responseData
     */

    /**
     * @callback postResponseManageCallback
     * @param {HTMLElement} contentLoader
     */

    /**
     * @param {Object.<string, string>} queryParameters
     * @param {responseManageCallback} responseManageCallback
     * @param {postResponseManageCallback} postResponseManageCallback
     */
    async #loadContent(queryParameters, responseManageCallback, postResponseManageCallback) {
        this.#showPlaceholder();
        const responseData = await endpoint.executeEndPointByName(this.endpointName, queryParameters);

        this.#setContent(responseManageCallback(responseData));
        postResponseManageCallback(this.element);
    }

    /**
     * @param {(string | Node)[]} nodes
     */
    #setContent(nodes) {
        this.containerTag.replaceChildren(nodes);
    }

    /**
     * @param {Object} event
     * @param {Object} event.detail
     * @param {Object} event.detail.content
     * @param {number} event.detail.content.page
     * @param {responseManageCallback} event.detail.content.responseManageCallback
     * @param {postResponseManageCallback} event.detail.content.postResponseManageCallback
     */
    async handleMessageChangeContent({ detail: { content } }) {
        let queryParameters = this.endpointQueryParameters;

        queryParameters.page = content.page;
        await this.#loadContent(queryParameters, content.responseManageCallback, content.postResponseManageCallback);
    }
}