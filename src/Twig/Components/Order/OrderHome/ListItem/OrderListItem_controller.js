import HomeListItemController from 'App/Twig/Components/HomeSection/HomeList/ListItem/HomeListItem_controller';
import * as endpoint from 'App/modules/ApiEndpoints';

export default class extends HomeListItemController {

    /**
     * @type {HTMLButtonElement}
     */
    #buttonItemBought;

    connect() {
        super.connect();

        this.#buttonItemBought = this.element.querySelector('[data-js-item-bought]');
        const itemData = this.getItemData();

        this.#setOrderBought(itemData.bought)
    }

    /**
     * @param {boolean} bought
     */
    #setOrderBought(bought) {
        if (bought) {
            this.#buttonItemBought.classList.add('order-list-item__button-bought--bought');
            this.#buttonItemBought.title = this.#buttonItemBought.dataset.orderBoughtTitle;
        } else {
            this.#buttonItemBought.classList.remove('order-list-item__button-bought--bought');
            this.#buttonItemBought.title = this.#buttonItemBought.dataset.orderNotBoughtTitle;
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
        this.#setOrderBought(bought);
    }
}