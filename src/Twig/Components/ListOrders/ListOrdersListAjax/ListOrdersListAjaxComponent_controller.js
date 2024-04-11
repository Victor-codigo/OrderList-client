import ItemsListAjaxController from 'App/Twig/Components/HomeSection/ItemsListAjax/ItemsListAjaxComponent_controller';
import ListItems from 'App/modules/ListItems';

export default class extends ItemsListAjaxController {
    /**
     * @param {object} responseData
     * @param {number} responseData.page
     * @param {number} responseData.pages_total
     * @param {array}  responseData.items
     * @param {array}  responseData.list_orders
     * @returns {ListItems}
     */
    responseManageCallback(responseData) {
        return super.responseManageCallback({
            page: responseData.page,
            pages_total: responseData.pages_total,
            items: responseData.list_orders
        });
    }
}