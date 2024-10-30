import HomeListItemController from 'App/Twig/Components/HomeSection/HomeList/ListItem/HomeListItem_controller';
import * as endpoint from 'App/modules/ApiEndpoints';
import * as communication from 'App/modules/ControllerCommunication';

export default class extends HomeListItemController {

    /**
     * @type {HTMLButtonElement}
     */
    #buttonItemBought;

    connect() {
        super.connect();

        this.#buttonItemBought = this.element.querySelector('[data-js-item-bought]');
        const itemData = this.getItemData();


        if (this.interactive) {
            this.#setOrderBought(itemData.id, itemData.bought, false);
        }
    }

    /**
     * @param {string} orderId
     * @param {boolean} bought
     * @param {boolean} sendMessageBought
     */
    #setOrderBought(orderId, bought, sendMessageBought) {

        if (bought) {
            this.#buttonItemBought.classList.add('order-list-item__button-bought--bought');
            this.#buttonItemBought.title = this.#buttonItemBought.dataset.orderBoughtTitle;
        } else {
            this.#buttonItemBought.classList.remove('order-list-item__button-bought--bought');
            this.#buttonItemBought.title = this.#buttonItemBought.dataset.orderNotBoughtTitle;
        }

        if (sendMessageBought) {
            this.#sendMessageOrderBoughtToParent(orderId, bought);
        }
    }

    /**
     * @throws {Error}
     */
    async toggleMarkAsBought() {
        const itemData = this.getItemData();
        const bought = itemData.bought ? false : true;
        await endpoint.orderBought(itemData.id, itemData.groupId, bought);

        itemData.bought = bought;

        this.setItemData(itemData);
        this.#setOrderBought(itemData.id, bought, true);
    }

    /**
     * @param {string} orderId
     * @param {boolean} bought
     */
    #sendMessageOrderBoughtToParent(orderId, bought) {
        communication.sendMessageToParentController(this.element, 'orderBoughtChanged', {
            id: orderId,
            bought: bought
        });
    }
}