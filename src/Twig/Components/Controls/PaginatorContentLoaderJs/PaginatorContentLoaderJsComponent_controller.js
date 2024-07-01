import { Controller } from '@hotwired/stimulus';
import * as communication from 'App/modules/ControllerCommunication';

/**
 * @event ContentLoaderJsComponent:changeContent
 * @event PaginatorContentLoaderJsComponent:connected
 */
export default class extends Controller {
    /**
     * @type {function}
     */
    responseManageCallback;

    /**
     * @type {function}
     */
    postResponseManageCallback;

    /**
     * @type {HTMLDivElement}
     */
    contentLoaderJsComponent;

    /**
     * @type {HTMLDivElement}
     */
    paginatorJsComponent;

    /**
     * @type {HTMLDivElement}
     */
    alphanumericFilterComponent;

    connect() {
        this.contentLoaderJsComponent = this.element.querySelector('[data-controller="ContentLoaderJsComponent"]');
        this.paginatorJsComponent = this.element.querySelector('[data-controller="PaginatorJsComponent"]');
        this.alphanumericFilterComponent = this.element.querySelector('[data-controller="AlphanumericFilterComponent"]');

        this.#sendMessageConnectedToParent();
    }

    /**
     * @param {string} letter
     * @param {number} page
     */
    #sendMessageContentChangeToContentLoaderJsComponent(letter, page) {
        if (typeof this.responseManageCallback == 'undefined') {
            throw new Error('PaginatorLoaderJsComponent: Not responseManagerCallback defined');
        }

        communication.sendMessageToChildController(this.contentLoaderJsComponent, 'changeContent', {
            page: page,
            letter: letter,
            responseManageCallback: this.responseManageCallback,
            postResponseManageCallback: this.postResponseManageCallback,
        });
    }

    /**
     * @param {string} letter
     */
    #sendMessageSetLetterFilterToAlphanumericFilter(letter) {
        communication.sendMessageToChildController(this.alphanumericFilterComponent, 'setLetterFilter', {
            letter: letter
        });
    }

    #sendMessageConnectedToParent() {
        communication.sendMessageToParentController(this.element, 'connected');
    }

    /**
     * @param {number} page
     */
    #sendMessagePageChangeToPaginatorJsComponent(page) {
        communication.sendMessageToChildController(this.paginatorJsComponent, 'pageChange', {
            page: page
        });
    }

    /**
     * @param {Object} event
     * @param {Object} event.detail
     * @param {Object} event.detail.content
     * @param {number} event.detail.content.page
     */
    handleMessageChangePage({ detail: { content } }) {
        this.#sendMessageContentChangeToContentLoaderJsComponent('A', content.page);
    }

    /**
     * @param {Object} event
     * @param {Object} event.detail
     * @param {Object} event.detail.content
     * @param {string} event.detail.content.letter
     */
    handleMessageChangeLetterFilter({ detail: { content } }) {
        this.#sendMessageContentChangeToContentLoaderJsComponent(content.letter, 1);
    }

    /**
     * @param {Object} event
     * @param {Object} event.detail
     * @param {Object} event.detail.content
     * @param {Object} event.detail.content.responseManageCallback
     * @param {Object} event.detail.content.postResponseManageCallback
     */
    handleMessageInitialize({ detail: { content } }) {
        this.responseManageCallback = content.responseManageCallback;
        this.postResponseManageCallback = content.postResponseManageCallback;
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {boolean} event.detail.content.showedFirstTime
     * @param {HTMLElement} event.detail.content.triggerElement
     */
    handleMessageBeforeShowed({ detail: { content } }) {
        this.#sendMessagePageChangeToPaginatorJsComponent(1);
        this.#sendMessageSetLetterFilterToAlphanumericFilter('A');
    }
}