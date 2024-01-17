import { Controller } from '@hotwired/stimulus';
import * as event from '../../../../../assets/modules/Event';

/**
 * @event ContentLoaderJsComponentEventHandler:onContentChange
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

        event.dispatch(this.contentLoaderJsComponent, 'ContentLoaderJsComponentEventHandler', 'onContentChange', {
            detail: {
                content: {
                    page: page,
                    responseManageCallback: this.responseManageCallback,
                    postResponseManageCallback: this.postResponseManageCallback,
                }
            }
        });
    }

    #triggerConnected() {
        this.dispatch('onConnected');
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