import { Controller } from '@hotwired/stimulus';
import * as communication from 'App/modules/ControllerCommunication';

export default class extends Controller {
    setImageAvatarAsRemoved(event) {
        let imageRemovedField = this.element.querySelector('[data-js-image-remove]');

        if (event.detail.imageRemove) {
            imageRemovedField.value = "true";

            return;
        }

        imageRemovedField.removeAttribute('value');
    }
}
