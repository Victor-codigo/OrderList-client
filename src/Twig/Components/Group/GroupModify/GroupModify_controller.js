import { Controller } from '@hotwired/stimulus';
import * as form from '/assets/modules/form';


export default class extends Controller {

    static targets = [
        'imageAvatar'
    ];

    connect() {
        this.formValidate();
    }

    formValidate() {
        form.validate(this.element);
    }

    setImageAvatarAsRemoved(event) {
        let imageRemovedField = this.element.querySelector('[data-js-image-remove]');

        if (event.detail.imageRemove) {
            imageRemovedField.value = "true";

            return;
        }

        imageRemovedField.removeAttribute('value');
    }
}
