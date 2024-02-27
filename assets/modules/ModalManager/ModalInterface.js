import Modal from "App/modules/ModalManager/Modal";

export default class ModalInterface {
    /**
     * @param {Modal} modalCurrent
     * @param {Modal} modalNew
     */
    closeCurrentAndOpenNew(modalCurrent, modalNew) {
        throw new Error('This method is not implemented');
    }

    /**
     * @param {Modal} modal
     */
    close(modal) {
        throw new Error('This method is not implemented');
    }
}