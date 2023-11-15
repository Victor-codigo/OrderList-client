import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {
        this.shopName = this.element.querySelector('[data-js-shop-name]').innerHTML.trim();
        this.shopDescription = this.element.querySelector('[data-js-shop-description]').innerHTML.trim();
        this.shopImage = this.element.querySelector('[data-js-shop-image]').src;
    }

    triggerOnShopRemove() {
        this.dispatch('onShopRemoveEvent', {
            detail: {
                content: {
                    shopId: this.element.dataset.shopId,
                    shopName: this.element.dataset.shopName
                }
            }
        });
    }

    triggerOnShopModify() {
        this.dispatch('onShopModifyEvent', {
            detail: {
                content: {
                    shopId: this.element.dataset.shopId,
                    shopName: this.shopName,
                    shopDescription: this.shopDescription,
                    shopImage: this.shopImage
                }
            }
        });
    }
}
