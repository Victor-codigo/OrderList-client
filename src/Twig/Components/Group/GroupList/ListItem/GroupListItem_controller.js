import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {
        this.groupIdInput = this.element.querySelector('[data-js-group-id]');
        this.groupId = this.groupIdInput.value;
        this.groupName = this.groupIdInput.dataset.groupName;
        this.groupRemoveButton = this.element.querySelector('[data-js-group-remove-button]');
        this.removeClickListener = this.groupRemoveButton.addEventListener('click', this.groupRemove.bind(this));
    }

    groupRemove() {
        this.dispatch('removeGroup', {
            detail: {
                groupId: this.groupId,
                groupName: this.groupName
            }
        });
    }
}
