import { Controller } from '@hotwired/stimulus';
import * as form from '../../../../../assets/modules/form';
import * as encodedUrlParameter from '../../../../../assets/modules/EncodedUrlParameter';


export default class extends Controller {
    connect() {
        this.shopNameTag = this.element.querySelector('[data-js-shop-name]');
        this.shopDescriptionTag = this.element.querySelector('[data-js-shop-description]');
        this.shopAvatarTag = this.element.querySelector('[data-controller="ImageAvatarComponent"]')
        this.formValidate();
    }

    formValidate() {
        form.validate(this.element);
    }

    setImageAvatarAsRemoved(event) {
        let imageRemovedField = this.element.querySelector('[data-js-image-remove]');

        if (event.detail.imageRemove) {
            imageRemovedField.value = "true";

            return;
        }

        imageRemovedField.removeAttribute('value');
    }

    handleOnShopModifyEvent({ detail: { content } }) {
        this.setFormFieldValues(content);
    }

    setFormFieldValues(content) {
        this.shopNameTag.value = content.shopName;
        this.element.action = this.element.dataset.actionPlaceholder.replace(
            '{shop_name}',
            encodedUrlParameter.encodeUrlParameter(content.shopName)
        );
        this.shopDescriptionTag.value = content.shopDescription;
        this.triggerOnAvatarSetImageEvent(content.shopImage);
    }

    triggerOnAvatarSetImageEvent(shopImageUrl) {

        this.dispatch('onAvatarSetImageEvent', {
            detail: {
                content: {
                    shopImage: shopImageUrl
                }
            }
        });
    }
}
