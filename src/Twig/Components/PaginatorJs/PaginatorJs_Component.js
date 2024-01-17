import { Controller } from '@hotwired/stimulus';
import * as event from '../../../../assets/modules/Event';
import * as communication from '/assets/modules/ControllerCommunication';

const PAGE_RANGE = 2;
const PAGE_ACTIVE_STYLE_NAME = 'paginator-js__page--active';
const PAGE_DISABLED_STYLE_NAME = 'paginator-js__page--disabled';
const PAGE_TYPES = {
    PAGE: 'data-js-page',
    PAGE_PREVIOUS: 'data-js-page-previous',
    PAGE_NEXT: 'data-js-page-next',
    PAGE_SEPARATOR: 'separator'
};

/**
 * @event onPageChange
 */
export default class extends Controller {
    connect() {
        this.paginatorContainer = this.element.querySelector('[data-js-paginator-container]');
        this.pagesTags = this.element.querySelectorAll('[data-js-page],[data-js-page-previous],[data-js-page-next]');
        this.pagesTotal = parseInt(this.element.dataset.pagesTotal);
        this.pageCurrent = parseInt(this.element.dataset.pageCurrent);
        this.#updatePaginatorHtml();

        event.addEventListenerDelegate({
            element: this.element,
            elementDelegateSelector: '[data-js-page],[data-js-page-previous],[data-js-page-next]',
            eventName: 'click',
            callbackListener: this.#handlePageChangeEvent.bind(this)
        });
    }

    disconnect() {
        event.removeEventListenerDelegate(this.element, 'click');
    }

    /**
     * @param {HTMLElement} pageTarget
     * @param {Event} event
     */
    #handlePageChangeEvent(pageTarget, event) {
        this.#setPage(this.#getPageNumber(pageTarget));
    }

    /**
     * @param {Object} content
     * @param {int} content.pagesTotal
     */
    handlerPagesTotalEvent({ detail: { content } }) {
        this.#setPagesTotal(content.pagesTotal);
        this.#updatePaginatorHtml();
    }

    #triggerPageChangeEvent() {
        communication.sendMessageToParentController(this.element, 'onPageChange', {
            page: this.pageCurrent
        });
    }

    /**
     * @param {int} page
     * @throws {Error}
     */
    #setPage(page) {
        if (page === this.pageCurrent) {
            return;
        }

        if (page < 1 || page > this.pagesTotal) {
            throw new Error('Paginator: page out of range');
        }

        this.pagesTags.forEach((pageTag) => {
            if (pageTag.classList.contains(PAGE_ACTIVE_STYLE_NAME)) {
                pageTag.classList.remove([PAGE_ACTIVE_STYLE_NAME]);

                return;
            }

            if (pageTag.dataset.jsPage == page) {
                pageTag.classList.add([PAGE_ACTIVE_STYLE_NAME])
            }
        });

        this.pageCurrent = page;
        this.#updatePaginatorHtml();
        this.#triggerPageChangeEvent();
    }

    /**
     * @param {HTMLLIElement} pageTag
     * @returns {int}
     */
    #getPageNumber(pageTag) {
        if (pageTag.hasAttribute('data-js-page')) {
            return parseInt(pageTag.dataset.jsPage);
        }

        if (pageTag.hasAttribute('data-js-page-previous')) {
            return parseInt(pageTag.dataset.jsPagePrevious);
        }

        if (pageTag.hasAttribute('data-js-page-next')) {
            return parseInt(pageTag.dataset.jsPageNext);
        }

        return 1;
    }

    /**
     * @param {int} pagesTotal
     */
    #setPagesTotal(pagesTotal) {
        this.pagesTotal = pagesTotal;
    }

    /**
     * @returns {Object[]} {
     *  page:int,
     *  text:string,
     *  active: bool
     * }
     */
    #createPagesObjects() {
        const pageMin = this.pageCurrent - PAGE_RANGE < 1 ? 1 : this.pageCurrent - PAGE_RANGE;
        const pageMax = this.pageCurrent + PAGE_RANGE > this.pagesTotal ? this.pagesTotal : this.pageCurrent + PAGE_RANGE;
        let pages = [];

        if (this.pageCurrent > 1) {
            pages.push({
                page: this.pageCurrent - 1,
                text: this.element.dataset.pagePreviousText,
                active: false,
                type: PAGE_TYPES.PAGE_PREVIOUS
            });
        }

        if (pageMin > 1) {
            pages.push({
                page: 1,
                text: 1,
                active: 1 === this.pageCurrent,
                type: PAGE_TYPES.PAGE
            });
        }

        if (pageMin > 2) {
            pages.push({
                page: null,
                text: '...',
                active: false,
                type: PAGE_TYPES.PAGE_SEPARATOR
            });
        }

        for (let i = pageMin; i <= pageMax; ++i) {
            pages.push({
                page: i,
                text: i,
                active: i === this.pageCurrent,
                type: PAGE_TYPES.PAGE
            });
        }

        if (pageMax < this.pagesTotal - 1) {
            pages.push({
                page: null,
                text: '...',
                active: false,
                type: PAGE_TYPES.PAGE_SEPARATOR
            });
        }

        if (pageMax < this.pagesTotal) {
            pages.push({
                page: this.pagesTotal,
                text: this.pagesTotal,
                active: this.pageCurrent === this.pagesTotal,
                type: PAGE_TYPES.PAGE
            });
        }

        if (this.pageCurrent < this.pagesTotal) {
            pages.push({
                page: this.pageCurrent + 1,
                text: this.element.dataset.pageNextText,
                active: false,
                type: PAGE_TYPES.PAGE_NEXT
            });
        }

        return pages.length > 1 ? pages : [];
    }

    /**
     * @param {PAGE_TYPES} pageType
     * @param {int} pageNumber
     * @param {string} text
     * @param {bool} active
     *
     * @returns {HTMLLIElement}
     */
    #createPage(pageType, pageNumber, text, active) {
        const pageTag = document.createElement('li');
        const pageLinkTag = document.createElement('span');

        pageLinkTag.classList.add('page-link');
        pageTag.appendChild(pageLinkTag);

        pageType !== PAGE_TYPES.PAGE_SEPARATOR
            ? pageTag.setAttribute(pageType, pageNumber)
            : pageTag.classList.add(PAGE_DISABLED_STYLE_NAME);
        active
            ? pageTag.classList.add('paginator-js__page', PAGE_ACTIVE_STYLE_NAME)
            : pageTag.classList.add('paginator-js__page');

        pageLinkTag.innerHTML = text;

        return pageTag;
    }

    /**
     * @returns {HTMLLIElement[]}
     */
    #createPaginator() {
        let pagesTags = [];
        let pagesObjects = this.#createPagesObjects();

        pagesObjects.forEach(({ page, text, active, type }) => {
            pagesTags.push(this.#createPage(type, page, text, active));
        });

        return pagesTags;
    }

    #updatePaginatorHtml() {
        const pagesTags = this.#createPaginator();

        this.paginatorContainer.replaceChildren(...pagesTags);
    }
}