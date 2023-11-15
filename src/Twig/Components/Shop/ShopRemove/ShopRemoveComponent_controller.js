import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {
        this.messageTag = this.element.querySelector('[data-js-message]');
        this.messagePlaceholder = this.messageTag.dataset.placeholder;
        this.shopIdTag = this.element.querySelector('[data-js-shop-id]');
    }

    handleOnShopRemoveEvent({ detail: { content } }) {
        this.changeShopId(content.shopId);
        this.changePlaceholderShopName(content.shopName);
    }

    changePlaceholderShopName(shopName) {
        this.messageTag.innerHTML = this.messagePlaceholder.replace('{shop_placeholder}', shopName);
    }

    changeShopId(shopId) {
        this.shopIdTag.value = shopId;
    }
}