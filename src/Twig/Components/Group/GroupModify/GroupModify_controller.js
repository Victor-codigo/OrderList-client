import { Controller } from '@hotwired/stimulus';
import * as form from 'App/modules/form';
import * as communication from 'App/modules/ControllerCommunication';
import * as encodedUrlParameter from 'App/modules/EncodedUrlParameter';

const GROUP_NAME_PLACEHOLDER = '--group_id--';

export default class extends Controller {

    /**
     * @type {HTMLInputElement}
     */
    #groupNameTag;

    /**
     * @type {HTMLInputElement}
     */
    #groupDescriptionTag;

    connect() {
        this.#groupNameTag = this.element.querySelector('[data-js-group-name]');
        this.#groupDescriptionTag = this.element.querySelector('[data-js-group-description]');

        this.formValidate();
    }

    formValidate() {
        form.validate(this.element, null);
    }

    setImageAvatarAsRemoved(event) {
        let imageRemovedField = this.element.querySelector('[data-js-image-remove]');

        if (event.detail.imageRemove) {
            imageRemovedField.value = "true";

            return;
        }

        imageRemovedField.removeAttribute('value');
    }

    /**
     * @param {import('App/Config').ItemData} groupData
     */
    setFormFieldValues(groupData) {
        this.#groupNameTag.value = groupData.name;
        this.#groupDescriptionTag.value = groupData.description;
        this.element.action = this.element.dataset.actionPlaceholder.replace(
            GROUP_NAME_PLACEHOLDER,
            encodedUrlParameter.encodeUrlParameter(groupData.id)
        );
        this.sendMessageAvatarSetImageEventToImageAvatarComponent(groupData.image);
    }

    /**
     * @param {string} groupImageUrl
     */
    sendMessageAvatarSetImageEventToImageAvatarComponent(groupImageUrl) {
        communication.sendMessageToNotRelatedController(this.element, 'avatarSetImage', {
            imageUrl: groupImageUrl
        },
            'ImageAvatarComponent'
        );
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {object} event.detail.content.itemData
     */
    handleMessageHomeListItemModify({ detail: { content } }) {
        this.setFormFieldValues(content.itemData);
    }
}
