import ItemInfo_controller from 'App/Twig/Components/HomeSection/ItemInfo/ItemInfo_controller';
import * as html from 'App/modules/Html';
import * as config from 'App/Config';

export default class extends ItemInfo_controller {

    /**
     * @type {HTMLParagraphElement}
     */
    #addressTag;

    connect() {
        super.connect();

        this.#addressTag = this.element.querySelector('[data-js-item-address]');
    }

    /**
     * @param {config.ItemData} data
     */
    setItemData(data) {
        super.setItemData(data);

        this.#addressTag.innerHTML = html.escape(data.address === null ? '' : data.address);
    }
}