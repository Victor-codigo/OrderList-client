import { Controller } from '@hotwired/stimulus';
import * as form from '/assets/modules/form';
import * as encodedUrlParameter from '/assets/modules/EncodedUrlParameter';
import * as communication from '/assets/modules/ControllerCommunication';


const SHOP_NAME_PLACEHOLDER = '--shop_name--';

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

    handleMessageHomeListItemModify({ detail: { content } }) {
        this.setFormFieldValues(content);
    }

    setFormFieldValues(content) {
        this.shopNameTag.value = content.name;
        this.element.action = this.element.dataset.actionPlaceholder.replace(
            SHOP_NAME_PLACEHOLDER,
            encodedUrlParameter.encodeUrlParameter(content.name)
        );
        this.shopDescriptionTag.value = content.description;
        this.sendMessageAvatarSetImageEventToImageAvatarComponent(content.image);
    }

    sendMessageAvatarSetImageEventToImageAvatarComponent(shopImageUrl) {
        communication.sendMessageToNotRelatedController(this.element, 'avatarSetImage', {
            imageUrl: shopImageUrl
        },
            'ImageAvatarComponent'
        );
    }


}
