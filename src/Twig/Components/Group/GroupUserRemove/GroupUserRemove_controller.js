import { Controller } from '@hotwired/stimulus';

export default class extends Controller {

    connect() {
        this.groupRemoveId = this.element.querySelector('[data-js-group-id]');
        this.userRemoveId = this.element.querySelector('[data-js-user-id]');
    }

    setUserIdToRemove({ detail: { groupId, userId, userName } }) {
        this.groupRemoveId.value = groupId;
        this.userRemoveId.value = userId;
        this.setUserNameInMessage(userName);
    }

    setUserNameInMessage(userName) {
        let messageParagraph = this.element.querySelector('[data-js-message]');

        messageParagraph.innerHTML = messageParagraph.dataset.message.replace('#USER_REMOVE#', userName);
    }
}
