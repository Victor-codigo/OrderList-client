import { Controller } from '@hotwired/stimulus';
import * as html from 'App/modules/Html';

/**
 * @readonly
 * @enum {string}
 */
export const ALERT_TYPE = {
    DANGER: 'danger',
    INFO: 'info',
    SUCCESS: 'success',
};

export default class extends Controller {
    connect() {
        this.validationContainerTag = this.element.querySelector('[data-js-container]');
        this.listErrorsItemTemplateTag = this.element.querySelector('[data-js-item-template]');
    }

    /**
     * @param {object} validations
     * @param {string[]} validations.ok
     * @param {string[]} validations.errors
     * @param {ALERT_TYPE} type
     */
    #setValidations(validations, type) {
        const validationError = validations.ok.length === 0 ? true : false;
        const validationList = validationError ? validations.errors : validations.ok;
        const validationListItems = validationList.map((validation) => this.#createValidation(validation));
        this.validationContainerTag.replaceChildren(...validationListItems);
        this.#setValidationType(type);
    }

    /**
     * @param {string} validation
     *
     * @returns {HTMLElement}
     */
    #createValidation(validation) {
        const listItem = this.listErrorsItemTemplateTag.content.cloneNode(true);
        const text = listItem.querySelector('[data-js-item-template-text]');

        text.innerHTML = html.escape(validation);

        return listItem;
    }

    #show() {
        this.element.style.display = 'block';
    }

    #hide() {
        this.element.style.display = 'none';
    }

    /**
     * @param {string} type
     */
    #setValidationType(type) {
        this.element.classList.remove('text-bg-light', 'text-bg-danger', 'text-bg-success');

        switch (type) {
            case ALERT_TYPE.DANGER:
                this.element.classList.add('text-bg-danger');
                break;
            case ALERT_TYPE.SUCCESS:
                this.element.classList.add('text-bg-success');
                break;
            default:
                this.element.classList.add('text-bg-light');
        }
    }

    /**
     * @param {object} event
     * @param {object} detail
     * @param {object} detail.content
     * @param {object} detail.content.validations
     * @param {string[]} detail.content.validations.ok
     * @param {string[]} detail.content.validations.errors
     * @param {ALERT_TYPE} detail.content.type
     */
    handleMessageAddValidationErrors({ detail: { content } }) {
        this.#setValidations(content.validations, content.type);
        this.#show();
    }
}
