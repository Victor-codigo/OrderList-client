import { Controller } from '@hotwired/stimulus';
import * as form from '/assets/modules/form';
import * as encodedUrlParameter from '/assets/modules/EncodedUrlParameter';
import * as communication from '/assets/modules/ControllerCommunication';

const PRODUCT_NAME_PLACEHOLDER = '--product_name--';

export default class extends Controller {

    connect() {
        this.productNameTag = this.element.querySelector('[data-js-product-name]');
        this.productDescriptionTag = this.element.querySelector('[data-js-product-description]');
        this.productAvatarTag = this.element.querySelector('[data-controller="ImageAvatarComponent"]')

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

    handleMessageHomeListItemModify({ detail: { content } }) {
        this.setFormFieldValues(content);
    }

    setFormFieldValues(content) {
        this.productNameTag.value = content.name;
        this.element.action = this.element.dataset.actionPlaceholder.replace(
            PRODUCT_NAME_PLACEHOLDER,
            encodedUrlParameter.encodeUrlParameter(content.name)
        );
        this.productDescriptionTag.value = content.description;
        this.sendMessageAvatarSetImageEventToImageAvatarComponent(content.image);
    }

    sendMessageAvatarSetImageEventToImageAvatarComponent(productImageUrl) {
        communication.sendMessageToNotRelatedController(this.element, 'avatarSetImage', {
            imageUrl: productImageUrl
        },
            'ImageAvatarComponent'
        );
    }


}
