import ItemRemoveController from 'App/Twig/Components/HomeSection/ItemRemove/ItemRemoveComponent_controller';

export default class extends ItemRemoveController {
    connect() {
        super.connect();

        this.formRemoveItemIdFieldName = `${this.element.name}[list_orders_id][]`;
    }
}