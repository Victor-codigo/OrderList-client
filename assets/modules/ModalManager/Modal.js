import Chain from 'App/modules/ModalManager/Chain';

export default class Modal {
    /**
     * @type {Chain}
     */
    #chain;

    /**
     * @type {string}
     */
    #modalId;

    /**
     * @type {HTMLElement}
     */
    #modalTag;

    /**
     * @param {Chain} chain
     * @param {string} modalId
     * @param {HTMLElement} modalTag
     */
    constructor(chain, modalId, modalTag) {
        this.#chain = chain;
        this.#modalId = modalId;
        this.#modalTag = modalTag;
    }

    /**
     * @returns   {string}
     */
    getModalId() {
        return this.#modalId;
    }

    /**
     * @returns {HTMLElement}
     */
    getModalTag() {
        return this.#modalTag;
    }

    /**
     * @returns {Chain}
     */
    getChain() {
        return this.#chain;
    }
}