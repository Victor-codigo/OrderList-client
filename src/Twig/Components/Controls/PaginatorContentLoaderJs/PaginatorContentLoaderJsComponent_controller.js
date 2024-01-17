import { Controller } from '@hotwired/stimulus';
import * as communication from '/assets/modules/ControllerCommunication';

/**
 * @event ContentLoaderJsComponent:onContentChange
 * @event PaginatorContentLoaderJsComponent:onConnected
 */
export default class extends Controller {
    responseManageCallback;
    postResponseManageCallback;

    connect() {
        this.contentLoaderJsComponent = this.element.querySelector('[data-controller="ContentLoaderJsComponent"]');

        this.#triggerConnected();
    }

    /**
     * @param {int} page
     */
    #triggerContentChange(page) {
        if (typeof this.responseManageCallback == 'undefined') {
            throw new Error('PaginatorLoaderJsComponent: Not responseManagerCallback defined');
        }

        communication.sendMessageToChildController(this.contentLoaderJsComponent, 'onContentChange', {
            page: page,
            responseManageCallback: this.responseManageCallback,
            postResponseManageCallback: this.postResponseManageCallback,
        });
    }

    #triggerConnected() {
        communication.sendMessageToParentController(this.element, 'onConnected');
    }

    /**
     * @param {Object} content
     * @param {int} content.page
     */
    handlerPaginatorPaginatorPageChange({ detail: { content } }) {
        this.#triggerContentChange(content.page);
    }

    /**
     * @param {Object} content
     * @param {Object} content.responseManageCallback
     * @param {Object} content.postResponseManageCallback
     */
    handlerPaginatorContentLoaderJsInitialize({ detail: { content } }) {
        this.responseManageCallback = content.responseManageCallback;
        this.postResponseManageCallback = content.postResponseManageCallback;
    }
}