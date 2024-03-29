import ItemInfo from "App/Twig/Components/HomeSection/ItemInfo/ItemInfo_controller";
import * as html from 'App/modules/Html';
import * as config from 'App/Config';

/**
 * @typedef {config.ItemData} ListOrdersData
 * @property {string} dateToBuy
 */

export default class extends ItemInfo {
    /**
     * @type {HTMLHeadElement}}
     */
    #dateToBuyTag;

    connect() {
        super.connect();

        this.#dateToBuyTag = this.element.querySelector('[data-js-item-date-to-buy]');
    }

    /**
     * @param {ListOrdersData} data
     */
    setItemData(data) {
        super.setItemData(data);

        let dateToBuy = '---';
        if (data.dateToBuy !== null) {
            dateToBuy = html.escape(this.getDataLocale(data.dateToBuy, config.dateTimeFormat));
        }

        this.#dateToBuyTag.innerHTML = dateToBuy;
    }
}