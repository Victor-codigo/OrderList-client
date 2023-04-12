import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {
        this.groupId = this.element.querySelector('[data-js-group-id]').value;
        this.groupRemoveButton = this.element.querySelector('[data-js-group-remove-button]');
        this.removeClickListener = this.groupRemoveButton.addEventListener('click', this.groupRemove.bind(this));
    }

    groupRemove() {
        this.dispatch('onGroupRemoveEvent', { detail: { groupId: this.groupId } });
    }
}
