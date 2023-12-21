import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        this.image = this.element.querySelector('[data-js-image]');
        this.buttonImageRemove = this.element.querySelector('[data-js-button-remove]');
        this.buttonImageRemove.addEventListener('click', this.removeImage.bind(this));

        this.buttonImageRemoveUndo = this.element.querySelector('[data-js-button-remove-undo]');
        this.buttonImageRemoveUndo.addEventListener('click', this.removeImageUndo.bind(this));

        this.setImage(this.image.src);
    }

    setImage(imageUrl) {
        this.image.src = imageUrl;
        this.imageUrl = this.image.src;

        if (this.imageUrl === this.image.dataset.noAvatar) {
            this.buttonImageRemove.classList.add('d-none');
            this.buttonImageRemoveUndo.classList.add('d-none');

            return;
        }

        this.buttonImageRemoveUndo.classList.add('d-none');
        this.buttonImageRemove.classList.remove('d-none');

    }

    removeImage() {
        this.image.src = this.image.dataset.noAvatar;
        this.buttonImageRemove.classList.add('d-none');
        this.buttonImageRemoveUndo.classList.remove('d-none');

        this.dispatch('imageRemoved', { detail: { imageRemove: true } });
    }

    removeImageUndo() {
        this.image.src = this.imageUrl;
        this.buttonImageRemoveUndo.classList.add('d-none');
        this.buttonImageRemove.classList.remove('d-none');

        this.dispatch('imageRemovedUndo', { detail: { imageRemove: false } });
    }

    handleOnSetImageEvent({ detail: { content } }) {
        this.setImage(content.imageUrl);
    }
}