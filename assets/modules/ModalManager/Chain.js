import Modal from "App/modules/ModalManager/Modal";

export default class Chain {
    /**
     * @type {string}
     */
    #name;
    /**
     * @type {Modal[]}
     */
    #modals;

    /**
     * @param {string} name
     * @param {Modal[]} modals
     */
    constructor(name, modals) {
        this.#name = name;
        this.#modals = modals;
    }

    /**
     * @returns {string}
     */
    getName() {
        return this.#name;
    }

    /**
     * @returns {Modal[]}
     */
    getModals() {
        return this.#modals;
    }

    /**
     * @param {string} modalId
     */
    getModalByModalId(modalId) {
        const modalSelected = this.#modals.filter((modal) => modal.getModalId() === modalId);

        return modalSelected.length > 0 ? modalSelected[0] : null;
    }

    /**
     * @param {Modal} modal
     */
    addModal(modal) {
        const modalByModalId = this.getModalByModalId(modal.getModalId());

        if (modalByModalId !== null) {
            throw new Error('The modal is already added');
        }

        this.#modals.push(modal);
    }
}