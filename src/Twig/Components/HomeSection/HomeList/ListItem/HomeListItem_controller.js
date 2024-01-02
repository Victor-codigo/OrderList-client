import { Controller } from '@hotwired/stimulus';
import * as event from '../../../../../../assets/modules/Event';


export default class extends Controller {
    connect() {
        this.itemName = this.element.querySelector('[data-js-item-name]').innerHTML.trim();
        this.itemDescription = this.element.querySelector('[data-js-item-description]').innerHTML.trim();
        this.itemImage = this.element.querySelector('[data-js-item-image]').src;

        this.checkbox = this.element.querySelector('[data-js-checkbox]');
        this.checkbox.addEventListener('change', this.#triggerOnHomeListItemSelected.bind(this));
    }

    disconnect() {
        this.checkbox.removeEventListener('change', this.#triggerOnHomeListItemSelected);
    }

    triggerOnHomeListItemRemoveEvent() {
        event.dispatch(window, 'ItemRemoveComponent', 'onHomeListItemRemoveEvent', {
            detail: {
                content: {
                    'items': [{
                        'id': this.element.dataset.itemId,
                        'name': this.element.dataset.itemName
                    }]
                }
            }
        });
    }

    triggerOnHomeListItemModify() {
        this.dispatch('onHomeListItemModifyEvent', {
            detail: {
                content: {
                    id: this.element.dataset.itemId,
                    name: this.itemName,
                    description: this.itemDescription,
                    image: this.itemImage
                }
            }
        });
    }

    #triggerOnHomeListItemSelected() {
        this.dispatch('onHomeListItemSelectedEvent', {
            detail: {
                content: {
                    id: this.element.dataset.itemId
                }
            }
        });
    }
}
