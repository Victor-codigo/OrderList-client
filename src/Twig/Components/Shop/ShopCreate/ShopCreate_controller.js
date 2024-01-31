import { Controller } from '@hotwired/stimulus';
import * as form from 'App/modules/form';
import * as communication from 'App/modules/ControllerCommunication'

export default class extends Controller {
    connect() {
        this.dropZoneComponentTag = this.element.querySelector('[data-controller="DropZoneComponent"]');
        this.alertComponentTag = this.element.querySelector('[data-controller="AlertComponent"]');
        this.formValidate();
    }

    formValidate() {
        form.validate(this.element, null);
    }

    clear() {
        this.element.reset();
        this.element.classList.remove('was-validated');
        this.alertComponentTag.style.display = 'none';
        this.#sendMessageClearToDropZone();
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {boolean} event.detail.content.showedFirstTime
     * @param {HTMLElement} event.detail.content.triggerElement
     */
    handleMessageBeforeShowed({ detail: { content } }) {
        this.clear();
    }

    #sendMessageClearToDropZone() {
        communication.sendMessageToChildController(this.dropZoneComponentTag, 'clear');
    }
}
