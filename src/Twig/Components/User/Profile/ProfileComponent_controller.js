import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        this.image = this.element.querySelector('[data-js-selector="avatar"]');
        this.imageRemoveButton = this.element.querySelector('[data-js-selector="image_remove_button"]');
        this.imageRemove = this.element.querySelector('[data-js-selector="image_remove"]');

        this.imageRemoveButton.addEventListener('click', this.toggleImageRemove.bind(this));
    }

    toggleImageRemove() {
         this.image.src = this.image.dataset.noAvatar;
         this.imageRemove.value = true;
    }
}
