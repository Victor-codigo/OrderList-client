import * as bootstrap from 'bootstrap';
import * as communication from 'App/modules/ControllerCommunication';

/**
 * @param {HTMLElement} modalCurrentTag
 * @param {string} modalNewId
 * @param {HTMLElement} [messageControlSender]
 * @param {string} [messageName]
 * @param {object} [content]
 */
export function closeCurrentAndOpenNew(modalCurrentTag, modalNewId, messageControlSender, messageName, content) {
    const modalCurrentInstance = bootstrap.Modal.getInstance(modalCurrentTag);
    const modalNewInstance = new bootstrap.Modal('#' + modalNewId);

    modalCurrentInstance.hide();

    if (typeof messageControlSender !== 'undefined' && typeof messageName !== 'undefined' && typeof content !== 'undefined') {
        communication.sendMessageToNotRelatedController(messageControlSender, messageName, content);
    }

    modalNewInstance.show();
}