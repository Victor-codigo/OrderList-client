import ItemsListAjaxController from 'App/Twig/Components/HomeSection/ItemsListAjax/ItemsListAjaxComponent_controller';
import ListItems from 'App/modules/ListItems';
import { MODAL_CHAINS } from 'App/Config';

export default class extends ItemsListAjaxController {
    /**
     * @param {object} responseData
     * @param {number} responseData.page
     * @param {number} responseData.pages_total
     * @param {array} responseData.items
     * @param {array} responseData.shops
     * @returns {ListItems}
     */
    responseManageCallback(responseData) {
        return super.responseManageCallback({
            page: responseData.page,
            pages_total: responseData.pages_total,
            items: responseData.shops
        });
    }

    openModalCreateItem() {
        const chainCurrentName = this.modalManager.getChainCurrent().getName();

        this.modalManager.openNewModal(MODAL_CHAINS[chainCurrentName].modals.shopList.open.shopCreate);
    }
}