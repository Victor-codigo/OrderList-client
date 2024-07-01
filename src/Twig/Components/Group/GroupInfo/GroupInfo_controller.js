import ItemInfo_controller from 'App/Twig/Components/HomeSection/ItemInfo/ItemInfo_controller';
import * as config from 'App/Config';

export default class extends ItemInfo_controller {
    /**
     * @type {HTMLSpanElement}
     */
    #adminTag;

    connect() {
        super.connect();

        this.#adminTag = this.element.querySelector('[data-js-user-admin]');
    }

    /**
     * @param {config.ItemData} data
     */
    setItemData(data) {
        super.setItemData(data);

        if (data.admin) {
            this.#adminTag.hidden = false;
        }
    }

    modalReset() {
        this.#adminTag.hidden = true;
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {config.ItemData} event.detail.content.itemData
     */
    handleMessageHomeListItemInfo({ detail: { content } }) {
        this.modalReset();

        super.handleMessageHomeListItemInfo({ detail: { content } });
    }
}