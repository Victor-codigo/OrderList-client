import Modal from "App/modules/ModalManager/Modal";

export default class ModalMessage {
    /**
     * @type {Modal|null}
     */
    #modalBefore = null;

    /**
     * @type {object}
     */
    #data = {};

    /**
     * @param {Modal|null} modalBefore
     * @param {object} data
     */
    constructor(modalBefore, data) {
        this.#modalBefore = modalBefore;
        this.#data = data;
    }

    /**
     * @returns {Modal}
     */
    getModalBefore() {
        return this.#modalBefore;
    }

    /**
     * @returns {object}
     */
    getData() {
        return this.#data;
    }
}