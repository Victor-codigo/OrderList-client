import Chain from "App/modules/ModalManager/Chain";
import Modal from "App/modules/ModalManager/Modal";
import ModalInterface from "App/modules/ModalManager/ModalInterface";
import ModalMessage from "App/modules/ModalManager/ModalMessage";
import ModalChainOpened from "App/modules/ModalManager/ModalChainOpened";

export default class ModalManager {
    /**
     * @type {ModalManager}
     */
    static #instance;

    /**
     * @type {Chain[]}
     */
    #modalChains = [];

    /**
     * @type {Modal|null}
     */
    #modalCurrent = null;

    /**
     * @type {ModalInterface}
     */
    #modalHandler;

    /**
     * @type {ModalMessage}
     */
    #modalDataShare = null;

    /**
     * @type {ModalChainOpened}
     */
    #modalChainOpened;

    /**
     * @param {ModalInterface} modalHandler
     */
    constructor(modalHandler) {
        if (typeof ModalManager.#instance !== 'undefined') {
            return ModalManager.#instance;
        }

        this.#modalHandler = modalHandler;
        this.#modalChainOpened = new ModalChainOpened();
        ModalManager.#instance = this;

        return ModalManager.#instance;
    }

    /**
     * @param {string} chainName
     * @returns {Chain|null}
     */
    #getChainByName(chainName) {
        if (this.#modalChains.length === 0) {
            return null;
        }

        const chainSelected = this.#modalChains.filter((chain) => chain.getName() === chainName);

        return chainSelected.length > 0 ? chainSelected[0] : null;
    }

    /**
     * @param {Chain} chain
     * @param {string} modalId
     *
     * @returns {Modal}
     */
    #createModal(chain, modalId) {
        const modalTag = document.getElementById(modalId);

        return new Modal(chain, modalId, modalTag);
    }

    /**
     * @returns {Modal|null}
     */
    getModalCurrent() {
        return this.#modalCurrent;
    }

    /**
     * @returns {Modal|null}
     */
    getModalBeforeInChain() {
        return this.#modalChainOpened.getModalBefore();
    }

    /**
     * @returns {Modal|null}
     */
    getModalOpenedBefore() {
        if (this.#modalDataShare === null) {
            return null;
        }

        return this.#modalDataShare.getModalBefore();
    }

    /**
     * @returns {object}
     */
    getModalOpenedBeforeData() {
        if (this.#modalDataShare === null) {
            return {};
        }

        return this.#modalDataShare.getData();
    }

    /**
     * @returns {Chain|null}
     */
    getChainCurrent() {
        const modal = this.getModalCurrent();

        if (modal === null) {
            return null;
        }

        return modal.getChain();
    }

    /**
     * @param {string} chainName
     */
    modalChainExists(chainName) {
        const chain = this.#getChainByName(chainName);

        return chain === null ? false : true;
    }

    /**
     * @param {Chain} chain
     */
    #chainRemove(chain) {
        const chainToRemoveIndex = this.#modalChains.findIndex((chainIterator) => chainIterator.getName() === chain.getName());

        if (chainToRemoveIndex === -1) {
            return;
        }

        this.#modalChains.splice(chainToRemoveIndex, 1);
    }

    /**
     * @param {string} modalId
     * @param {string} chainName
     *
     * @returns {ModalManager}
     */
    addModal(chainName, modalId) {
        let chain = this.#getChainByName(chainName);

        if (chain === null) {
            chain = new Chain(chainName, []);
            this.#modalChains.push(chain);
        }

        const modal = this.#createModal(chain, modalId);
        chain.addModal(modal);

        return this;
    }

    /**
     * @param {string} chainName
     * @param {string} modalId
     *
     * @throws {Error}
     */
    setModalCurrent(chainName, modalId) {
        const modal = this.#getChainByName(chainName).getModalByModalId(modalId);

        this.#modalCurrent = modal;
        this.#modalChainOpened.openNew(this.#modalCurrent);
    }

    /**
     * @param {string} modalNewId
     * @param {object} [dataToPassToNewModal]
     * @param {string} [modalChainName]
     *
     * @throws {Error}
     */
    openNewModal(modalNewId, dataToPassToNewModal = {}, modalChainName = null) {
        const modalNew = this.#getModalOrFail(modalNewId);

        this.#modalChainOpened.openNew(modalNew);
        this.#openModal(modalNew, dataToPassToNewModal);
    }

    /**
     * @param {object} [dataToPassToNewModal]
     *
     * @throws {Error}
     */
    openModalBefore(dataToPassToNewModal = {}) {
        const modalBefore = this.#modalChainOpened.openModalBefore();

        this.#openModal(modalBefore, dataToPassToNewModal);
    }

    /**
     * @param {string} modalId
     * @param {object} [dataToPassToNewModal]
     *
     * @throws {Error}
     */
    openModalAlreadyOpened(modalId, dataToPassToNewModal = {}) {
        const modal = this.#getModalOrFail(modalId);

        this.#modalChainOpened.openModalAlreadyOpened(modal);
        this.#openModal(modal, dataToPassToNewModal);
    }

    close() {
        this.#modalChainOpened.clear();
        this.#chainRemove(this.getChainCurrent());

        if (this.getModalCurrent() !== null) {
            this.#modalHandler.close(this.getModalCurrent());
        }

        this.#modalDataShare = null;
        this.#modalCurrent = null;
    }

    /**
     * @param {string} modalId
     * @param {string} modalChainName
     *
     * @returns {Modal}
     *
     * @throws {Error}
     */
    #getModalOrFail(modalId, modalChainName = null) {
        let chain = this.#modalCurrent.getChain();

        if (modalChainName !== null) {
            chain = this.#getChainByName(modalChainName);
        }

        if (chain === null) {
            throw new Error('Chain not found');
        }

        let modal = chain.getModalByModalId(modalId);

        if (modal === null) {
            throw new Error('Modal not found');
        }

        return modal;
    }

    /**
     * @param {Modal} modalNew
     * @param {object} [dataToPassToNewModal]
     *
     * @throws {Error}
     */
    #openModal(modalNew, dataToPassToNewModal = {}) {
        const modalCurrentAux = this.#modalCurrent;

        this.#modalDataShare = new ModalMessage(modalCurrentAux, dataToPassToNewModal);
        this.#modalCurrent = modalNew;
        this.#modalHandler.closeCurrentAndOpenNew(modalCurrentAux, modalNew);
    }
}