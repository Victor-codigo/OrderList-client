import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    /**
     * @type {HTMLImageElement}
     */
    #imageTag;

    /**
     * @type {HTMLDivElement}
     */
    #buttonImageRemoveTag;

    /**
     * @type {HTMLDivElement}
     */
    #buttonImageRemoveUndoTag;

    connect() {
        this.#imageTag = this.element.querySelector('[data-js-image]');
        this.#buttonImageRemoveTag = this.element.querySelector('[data-js-button-remove]');
        this.#buttonImageRemoveTag.addEventListener('click', this.removeImage.bind(this));
        this.#buttonImageRemoveUndoTag = this.element.querySelector('[data-js-button-remove-undo]');

        this.imageUrl = this.#imageTag.src;
        this.setImageButtons();

        this.#buttonImageRemoveUndoTag.addEventListener('click', this.removeImageUndo.bind(this));
    }

    setImage(imageUrl) {
        this.#imageTag.src = imageUrl;
        this.imageUrl = this.#imageTag.src;

        this.setImageButtons();
    }

    setImageButtons() {
        if (this.#imageTag.getAttribute('src') === this.#imageTag.dataset.noAvatar) {
            this.#buttonImageRemoveTag.classList.add('d-none');
            this.#buttonImageRemoveUndoTag.classList.add('d-none');
            this.#imageTag.classList.add('image-avatar__img--svg-theme');

            return;
        }

        this.#buttonImageRemoveUndoTag.classList.add('d-none');
        this.#buttonImageRemoveTag.classList.remove('d-none');
        this.#imageTag.classList.remove('image-avatar__img--svg-theme');
    }

    removeImage() {
        this.#imageTag.src = this.#imageTag.dataset.noAvatar;
        this.#buttonImageRemoveTag.classList.add('d-none');
        this.#buttonImageRemoveUndoTag.classList.remove('d-none');
        this.#imageTag.classList.add('image-avatar__img--svg-theme');

        this.dispatch('imageRemoved', { detail: { imageRemove: true } });
    }

    removeImageUndo() {
        this.#imageTag.src = this.imageUrl;
        this.#buttonImageRemoveUndoTag.classList.add('d-none');
        this.#buttonImageRemoveTag.classList.remove('d-none');
        this.#imageTag.classList.remove('image-avatar__img--svg-theme');

        this.dispatch('imageRemovedUndo', { detail: { imageRemove: false } });
    }

    handleMessageSetImage({ detail: { content } }) {
        this.setImage(content.imageUrl);
    }
}