import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        this.groupRemoveId = this.element.querySelector('[data-js-group-id]');
    }

    setGroupIdToRemove({ detail: { groupId } }) {
        this.groupRemoveId.value = groupId;
    }
}
