import { Controller } from '@hotwired/stimulus';
import * as communication from 'App/modules/ControllerCommunication';

/**
 * @event ContentLoaderJsComponent:changeContent
 * @event PaginatorContentLoaderJsComponent:connected
 */
export default class extends Controller {
    responseManageCallback;
    postResponseManageCallback;

    connect() {
        this.contentLoaderJsComponent = this.element.querySelector('[data-controller="ContentLoaderJsComponent"]');

        this.#sendMessageConnectedToParent();
    }

    /**
     * @param {int} page
     */
    #sendMessageContentChangeToContentLoaderJsComponent(page) {
        if (typeof this.responseManageCallback == 'undefined') {
            throw new Error('PaginatorLoaderJsComponent: Not responseManagerCallback defined');
        }

        communication.sendMessageToChildController(this.contentLoaderJsComponent, 'changeContent', {
            page: page,
            responseManageCallback: this.responseManageCallback,
            postResponseManageCallback: this.postResponseManageCallback,
        });
    }

    #sendMessageConnectedToParent() {
        communication.sendMessageToParentController(this.element, 'connected');
    }

    /**
     * @param {Object} content
     * @param {int} content.page
     */
    handleMessageChangePage({ detail: { content } }) {
        this.#sendMessageContentChangeToContentLoaderJsComponent(content.page);
    }

    /**
     * @param {Object} content
     * @param {Object} content.responseManageCallback
     * @param {Object} content.postResponseManageCallback
     */
    handleMessageInitialize({ detail: { content } }) {
        this.responseManageCallback = content.responseManageCallback;
        this.postResponseManageCallback = content.postResponseManageCallback;
    }
}