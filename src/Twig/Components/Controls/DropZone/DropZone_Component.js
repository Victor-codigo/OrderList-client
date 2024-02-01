import { Controller } from '@hotwired/stimulus';


export default class extends Controller {

    connect() {
        this.dropzoneTag = this.element.querySelector('[data-js-symfony-ux-dropzone]');
    }

    #clear() {
        let dropzoneController = this.application.getControllerForElementAndIdentifier(this.dropzoneTag, 'symfony--ux-dropzone--dropzone');

        dropzoneController.clear();
    }

    handleMessageClear() {
        this.#clear();
    }
}