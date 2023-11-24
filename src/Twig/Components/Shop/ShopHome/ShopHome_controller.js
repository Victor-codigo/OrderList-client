import { Controller } from '@hotwired/stimulus';


export default class extends Controller {

    connect() {
        this.shopsIdSelected = [];
        this.listItemsCheckboxes = this.element.querySelectorAll('[data-js-checkbox]');
        this.shopRemoveManyFormTag = this.element.querySelector('[data-js-form-remove-many-shops]');
        this.shopRemoveMultiButtonTag = this.element.querySelector('[data-js-form-remove-many-shops-button]');
        this.element.addEventListener('change', this.#shopsIdSelectedToggle.bind(this));

        this.#shopsIdSelectedAddAll();
        this.#buttonShopRemoveMultiToggle();
    }

    #buttonShopRemoveMultiToggle() {
        if (this.shopsIdSelected.length === 0) {
            this.shopRemoveMultiButtonTag.disabled = true;

            return;
        }

        this.shopRemoveMultiButtonTag.disabled = false;
    }

    #shopsIdSelectedToggle(event) {
        if (event.target.tagName !== 'input' && event.target.type !== 'checkbox') return;

        const listItem = event.target.closest('[data-shop-id]');

        if (!event.target.checked) {
            this.shopsIdSelected = this.shopsIdSelected.filter((shop) => shop.shopId !== listItem.dataset.shopId);
            this.#buttonShopRemoveMultiToggle();

            return;
        }

        this.shopsIdSelected.push({
            'shopId': listItem.dataset.shopId,
            'shopName': listItem.dataset.shopName
        });
        this.#buttonShopRemoveMultiToggle();
    }

    #shopsIdSelectedAddAll() {
        this.listItemsCheckboxes.forEach((checkbox) => {
            const listItem = checkbox.closest('[data-shop-id]');

            if (checkbox.checked) {
                this.shopsIdSelected.push({
                    'shopId': listItem.dataset.shopId,
                    'shopName': listItem.dataset.shopName
                });
            }
        });
    }

    triggerOnShopRemoveMulti() {
        this.dispatch('onShopRemoveMultiEvent', {
            detail: {
                content: {
                    'shops': this.shopsIdSelected
                }
            }
        });
    }

}
