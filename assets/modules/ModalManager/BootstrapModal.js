import * as bootstrap from 'bootstrap';
import ModalInterface from 'App/modules/ModalManager/ModalInterface';
import Modal from 'App/modules/ModalManager/Modal';

export default class BootstrapModal extends ModalInterface {
    /**
     * @param {Modal} modalCurrent
     * @param {Modal} modalNew
     */
    closeCurrentAndOpenNew(modalCurrent, modalNew) {
        const modalCurrentInstance = bootstrap.Modal.getInstance(modalCurrent.getModalTag());
        const modalNewInstance = new bootstrap.Modal(modalNew.getModalTag());

        modalCurrentInstance.hide();
        modalNewInstance.show();
    }

    /**
     * @param {Modal} modal
     */
    close(modal) {
        const modalInstance = bootstrap.Modal.getInstance(modal.getModalTag());

        modalInstance.hide();
    }
}