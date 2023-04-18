import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        this.groupRemoveId = this.element.querySelector('[data-js-group-id]');
    }

    setGroupIdToRemove({ detail: { groupId, groupName } }) {
        this.groupRemoveId.value = groupId;

        console.log(groupName);
        this.setGroupNameInMessage(groupName);
    }

    setGroupNameInMessage(groupName) {
        let messageParagraph = this.element.querySelector('[data-js-message]');

        messageParagraph.innerHTML = messageParagraph.dataset.message.replace('#GROUP_NAME#', groupName);
    }
}
