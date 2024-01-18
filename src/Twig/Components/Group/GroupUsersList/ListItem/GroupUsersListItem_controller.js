import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {
        let userIdInput = this.element.querySelector('[data-js-user-id]');

        this.userId = userIdInput.value;
        this.userName = userIdInput.dataset.userName;
        this.groupId = this.element.querySelector('[data-js-group-id]').value;
        this.groupRemoveButton = this.element.querySelector('[data-js-group-remove-button]');
        this.removeClickListener = this.groupRemoveButton.addEventListener('click', this.groupRemove.bind(this));
    }

    groupRemove() {
        this.dispatch('removeGroupUser', {
            detail: {
                groupId: this.groupId,
                userId: this.userId,
                userName: this.userName
            }
        });
    }
}
