import ItemsListAjaxController from 'App/Twig/Components/HomeSection/ItemsListAjax/ItemsListAjaxComponent_controller';
import ListItems from 'App/modules/ListItems';
import { MODAL_CHAINS } from 'App/Config';

export default class extends ItemsListAjaxController {
    /**
     * @param {object} responseData
     * @param {number} responseData.page
     * @param {number} responseData.pages_total
     * @param {array}  responseData.items
     * @param {array}  responseData.products
     * @returns {ListItems}
     */
    responseManageCallback(responseData) {
        return super.responseManageCallback({
            page: responseData.page,
            pages_total: responseData.pages_total,
            items: responseData.products
        });
    }
}