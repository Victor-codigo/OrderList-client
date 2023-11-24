import { Controller } from '@hotwired/stimulus';


export default class extends Controller {
    connect() {
        this.shopName = this.element.querySelector('[data-js-shop-name]').innerHTML.trim();
        this.shopDescription = this.element.querySelector('[data-js-shop-description]').innerHTML.trim();
        this.shopImage = this.element.querySelector('[data-js-shop-image]').src;

        this.checkbox = this.element.querySelector('[data-js-checkbox]');
        this.checkbox.addEventListener('change', this.#triggerOnShopSelected.bind(this));
    }

    triggerOnShopRemove() {
        this.dispatch('onShopRemoveEvent', {
            detail: {
                content: {
                    'shops': [{
                        'shopId': this.element.dataset.shopId,
                        'shopName': this.element.dataset.shopName
                    }]
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

    #triggerOnShopSelected() {
        this.dispatch('onShopSelectedEvent', {
            detail: {
                content: {
                    shopId: this.element.dataset.shopId
                }
            }
        });
    }
}
