import { Controller } from '@hotwired/stimulus';
import * as form from 'App/modules/form';

export default class extends Controller {
    connect() {
        this.formValidate();
    }

    formValidate() {
        form.validate(this.element, null);
    }

    setImageAvatarAsRemoved(event) {
        let imageRemovedField = this.element.querySelector('[data-js-image-remove]');

        if (event.detail.imageRemove) {
            imageRemovedField.value = "true";

            return;
        }

        imageRemovedField.value = "false";
    }
}
