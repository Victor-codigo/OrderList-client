import { Controller } from '@hotwired/stimulus';
import * as event from 'App/modules/Event';
import * as communication from 'App/modules/ControllerCommunication';

export default class extends Controller {
    connect() {
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
        const letterFiltersTags = this.element.querySelectorAll('[data-letter-filter]');

        letterFiltersTags.forEach((letterFilterTag) => {
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
     * @param {object} event
     * @param {object} event.detail
     * @param {object} event.detail.content
     * @param {object} event.detail.content.letter
     */
    handleMessageSetLetterFilter({ detail: { content } }) {
        this.#setButtonActive(content.letter);
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