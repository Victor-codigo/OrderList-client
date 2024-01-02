import ItemRemoveController from '../../HomeSection/ItemRemove/ItemRemoveComponent_controller';
export default class extends ItemRemoveController {
    connect() {
        super.connect();

        this.formRemoveItemIdFieldName = `${this.element.name}[shops_id][]`;
    }
}