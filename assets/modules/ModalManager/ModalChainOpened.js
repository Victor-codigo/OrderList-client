import Modal from "App/modules/ModalManager/Modal";

export default class ModalChainOpened {
    /**
     * @type {Modal[]}
     */
    #modalsOpened = [];

    clear() {
        this.#modalsOpened = [];
    }

    /**
     * @param {Modal} modalNewOpened
     */
    openNew(modalNewOpened) {
        this.#modalsOpened.push(modalNewOpened);
    }

    /**
     * @returns {Modal|null}
     *
     * @throws {Error}
     */
    openModalBefore() {
        if (this.#modalsOpened.length < 2) {
            throw new Error('Modal before does not exist');
        }

        this.#removeChainAsOf(this.#modalsOpened[this.#modalsOpened.length - 2]);

        return this.#modalsOpened[this.#modalsOpened.length - 1];
    }

    /**
     * @param {Modal} modal
     *
     * @returns {Modal|null}
     *
     * @throws {Error}
     */
    openModalAlreadyOpened(modal) {
        this.#removeChainAsOf(modal);

        return this.#modalsOpened[this.#modalsOpened.length - 1];
    }

    /**
     * @returns {Modal|null}
     */
    getModalBefore() {
        if (this.#modalsOpened.length < 2) {
            return null;
        }

        return this.#modalsOpened[this.#modalsOpened.length - 2];
    }

    /**
     * @param {Modal} modal
     *
     * @throws {Error}
     */
    #removeChainAsOf(modal) {
        const modalIndex = this.#getModalIndex(modal);

        if (modalIndex === null) {
            throw new Error('Modal is not in opened chain');
        }

        this.#modalsOpened.splice(modalIndex + 1);
    }

    /**
     * @param {Modal} modal
     *
     * @returns {number|null}
     */
    #getModalIndex(modal) {
        const index = this.#modalsOpened.findIndex((modalCurrent) => modalCurrent.getModalId() === modal.getModalId());

        return index === -1 ? null : index;
    }
}