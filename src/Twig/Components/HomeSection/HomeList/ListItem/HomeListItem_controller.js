import { Controller } from '@hotwired/stimulus';
import * as communication from 'App/modules/ControllerCommunication';


export default class extends Controller {
    connect() {
        this.itemName = this.element.querySelector('[data-js-item-name]').innerHTML.trim();
        this.itemDescription = this.element.querySelector('[data-js-item-description]').innerHTML.trim();
        this.itemImage = this.element.querySelector('[data-js-item-image]').src;

        this.checkbox = this.element.querySelector('[data-js-checkbox]');
        this.checkbox.addEventListener('change', this.#sendMessageHomeListItemSelectedToParent.bind(this));
    }

    disconnect() {
        this.checkbox.removeEventListener('change', this.#sendMessageHomeListItemSelectedToParent);
    }

    sendMessageHomeListItemRemoveEvent() {
        communication.sendMessageToNotRelatedController(this.element, 'removeHomeListItem', {
            items: [{
                id: this.element.dataset.itemId,
                name: this.element.dataset.itemName
            }]
        },
            'ItemRemoveComponent'
        );
    }

    sendMessageHomeListItemModifyToParent() {
        communication.sendMessageToParentController(this.element, 'homeListItemModify', {
            id: this.element.dataset.itemId,
            name: this.itemName,
            description: this.itemDescription,
            image: this.itemImage
        });
    }

    #sendMessageHomeListItemSelectedToParent() {
        communication.sendMessageToParentController(this.element, 'onHomeListItemSelectedEvent', {
            id: this.element.dataset.itemId
        });
    }
}
