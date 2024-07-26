import { Controller } from '@hotwired/stimulus';
import * as event from 'App/modules/Event';
import * as communication from 'App/modules/ControllerCommunication';
import * as apiEndpoint from 'App/modules/ApiEndpoints'
import { SECTIONS } from 'App/modules/Url';

export default class extends Controller {
    /**
     * @type {HTMLButtonElement[]}
     */
    #lettersFilterTags

    connect() {
        this.#lettersFilterTags = this.element.querySelectorAll('[data-letter-filter]');

        event.addEventListenerDelegate({
            element: this.element,
            elementDelegateSelector: '[data-letter-filter]',
            eventName: 'click',
            callbackListener: this.#handlePageChangeEvent.bind(this),
            eventOptions: {}
        });
    }

    disconnect() {
        event.removeEventListenerDelegate(this.element, 'click', this.#handlePageChangeEvent);
    }

    /**
     * @param {HTMLElement} pageTarget
     * @param {Event} event
     */
    #handlePageChangeEvent(pageTarget, event) {
        const letterCurrent = pageTarget.innerText;

        this.#setPage(letterCurrent);
    }

    /**
     * @param {string} letter
     * @throws {Error}
     */
    #setPage(letter) {
        this.#setButtonActive(letter);
        this.#sendMessagePageChangeEventToParent(letter);
    }

    /**
     * @param {string} letter
     */
    #setButtonActive(letter) {
        this.#lettersFilterTags.forEach((letterFilterTag) => {
            if (letterFilterTag.innerText.trim() === letter) {
                letterFilterTag.classList.add('active');
                letterFilterTag.setAttribute('aria-pressed', 'true');

                return;
            }

            letterFilterTag.classList.remove('active');
            letterFilterTag.setAttribute('aria-pressed', 'false');
        });
    }

    /**
     * @param {string[]} letters
     */
    #setButtonsEnabled(letters) {
        this.#lettersFilterTags.forEach((letterFilterTag) => {
            if (letters.includes(letterFilterTag.innerText.trim().toLowerCase())) {
                letterFilterTag.classList.remove('btn-outline-secondary');
                letterFilterTag.classList.add('btn-outline-primary');

                letterFilterTag.removeAttribute('disabled');

                return;
            }

            letterFilterTag.classList.remove('btn-outline-primary');
            letterFilterTag.classList.add('btn-outline-secondary');
            letterFilterTag.setAttribute('disabled', 'true');
        });
    }

    /**
     * @param {string} groupId
     * @param {object} section
     *
     * @returns {Promise<string[]>}
     *
     * @throws Error
     */
    async #getUsableLetters(groupId, section) {
        let letters;

        switch (section) {
            case SECTIONS.LIST_ORDERS:
                letters = await apiEndpoint.getListOrdersFirstLetter(groupId);
                break;
            case SECTIONS.SHOP:
                letters = await apiEndpoint.getShopsFirstLetter(groupId);
                break;
            case SECTIONS.PRODUCT:
                letters = await apiEndpoint.getProductsFirstLetter(groupId);
                break;

            default:
                throw new Error('Section not found');
        }

        return letters;
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {object} event.detail.content.letter
     */
    handleMessageSetLetterFilter({ detail: { content } }) {
        this.#setButtonActive(content.letter);
    }

    /**
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {object} event.detail.content.groupId
     * @param {string} event.detail.content.section
     */
    async handleMessageLoadLetters({ detail: { content } }) {
        const letters = await this.#getUsableLetters(content.groupId, content.section);

        this.#setButtonsEnabled(Array.from(letters));

        if (letters.length > 0) {
            this.#setPage(letters[0]);
        } else {
            this.#setPage('A');
        }
    }

    /**
     * @param {string} letterCurrent
     */
    #sendMessagePageChangeEventToParent(letterCurrent) {
        communication.sendMessageToParentController(this.element, 'changeLetterFilter', {
            letter: letterCurrent
        });
    }
}