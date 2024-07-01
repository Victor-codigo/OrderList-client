import ItemRemoveController from 'App/Twig/Components/HomeSection/ItemRemove/ItemRemoveComponent_controller';

export default class extends ItemRemoveController {
    connect() {
        super.connect();

        this.formRemoveItemIdFieldName = `${this.element.name}[notifications_id][]`;
    }

    /**
     * @param {object} items
     */
    loadComponentData(items) {
        let itemsNames = [];

        this.clearInputItemIds();
        items.forEach((item) => {
            this.createInputItemId(item.id);

            itemsNames.push(item.message);
        });

        this.changePlaceholderItemName(itemsNames);
    }
}