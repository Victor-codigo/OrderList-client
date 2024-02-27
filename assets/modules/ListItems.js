import * as html from 'App/modules/Html';
export default class ListItems {

    /**
     * @type {HTMLUListElement}
     */
    #listTag = null;
    get listTag() { return this.#listTag; }

    /**
     * @param {Object} listData
     * @param {Object} listData.htmlAttributes
     * @param {string[]} listData.cssClasses
     *
     * @param {Object[]} itemsData
     * @param {string} itemsData.name
     * @param {*} itemsData.data
     * @param {Object} itemsData.image
     * @param {string} itemsData.image.src
     * @param {string} itemsData.image.title
     * @param {string} itemsData.image.alt
     * @param {Object} itemsData.item
     * @param {Object} itemsData.item.htmlAttributes
     * @param {string[]} itemsData.item.cssClasses
     */
    constructor(listData, itemsData) {
        this.#listTag = this.#createList(listData, itemsData);
    }

    /**
     * @param {object} listData
     * @param {object} listData.htmlAttributes
     * @param {string[]} listData.cssClasses
     *
     * @param {Object[]} itemsData
     * @param {string} itemsData.name
     * @param {*} itemsData.data
     * @param {Object} itemsData.image
     * @param {string} itemsData.image.src
     * @param {string} itemsData.image.title
     * @param {string} itemsData.image.alt
     * @param {Object} itemsData.item
     * @param {Object} itemsData.item.htmlAttributes
     * @param {string[]} itemsData.item.cssClasses
     *
     * @returns {HTMLUListElement} list of items
     */
    #createList(listData, itemsData) {
        const list = document.createElement('ul');

        listData.cssClasses ? list.classList.add(...listData.cssClasses) : null;
        listData.htmlAttributes ? Object.entries(listData.htmlAttributes).forEach((attribute) => list.setAttribute(attribute[0], attribute[1])) : null;

        const items = itemsData.map((itemData) => this.#createItem(itemData));

        list.classList.add('list-group', 'list-group-flush');
        list.replaceChildren(...items);

        return list;
    }

    /**
     * @param {Object[]} itemsData
     * @param {string} itemsData.name
     * @param {*} itemsData.data
     * @param {Object} itemsData.image
     * @param {string} itemsData.image.src
     * @param {string} itemsData.image.title
     * @param {string} itemsData.image.alt
     * @param {Object} itemsData.list
     * @param {Object} itemsData.list.htmlAttributes
     * @param {string[]} itemsData.list.cssClasses
     * @param {Object} itemsData.item
     * @param {Object} itemsData.item.htmlAttributes
     * @param {string[]} itemsData.item.cssClasses
     *
     * @returns {HTMLElement} list of items
     */
    #createItem(itemData) {
        const itemContainer = this.#createItemContainer(itemData.data, itemData.item.htmlAttributes, itemData.item.cssClasses);
        const itemImage = this.#createItemImage(itemData.image.src, itemData.image.title, itemData.image.alt);
        const itemSpan = this.#createItemSpan(itemData.name);

        itemContainer.appendChild(itemImage);
        itemContainer.appendChild(itemSpan);

        return itemContainer;
    };

    /**
     * @param {*} data
     * @param {Object[]} htmlAttributes
     * @param {string[]} cssClasses
     *
     * @returns {HTMLElement}
     */
    #createItemContainer(data, htmlAttributes, cssClasses) {
        const listItem = document.createElement('li');
        const cssCLassesToSet = typeof cssClasses === 'object'
            ? cssClasses
            : [];
        const htmlAttributesToSet = typeof htmlAttributes === 'object'
            ? htmlAttributes
            : {};

        listItem.classList.add(
            'list-group-item',
            'list-group-item-action',
            'd-flex',
            'flex-row',
            'align-items-center',
            'list-items__item',
            ...cssCLassesToSet
        );

        listItem.setAttribute('role', 'button');
        listItem.setAttribute('data-data', JSON.stringify(data));
        Object.entries(htmlAttributesToSet).forEach((attribute) => listItem.setAttribute(attribute[0], attribute[1]));

        return listItem;
    }

    /**
     * @param {string} src
     * @param {string} title
     * @param {string} alt
     *
     * @returns {HTMLElement}
     */
    #createItemImage(src, title, alt) {
        const listItemImage = document.createElement('img');

        listItemImage.classList.add(
            'img-thumbnail',
            'flex-grow-0',
            'rounded-0',
            'border',
            'border-0',
            'border-start',
            'border-end',
            'item__image'
        );
        listItemImage.style.maxHeight = '50px';
        listItemImage.style.maxWidth = '50px';

        listItemImage.src = src;
        listItemImage.title = title;
        listItemImage.alt = alt;

        return listItemImage;
    }

    /**
     * @param {string} name
     *
     * @returns {HTMLElement}
     */
    #createItemSpan(name) {
        const listItemSpan = document.createElement('span');

        listItemSpan.classList.add('ms-2');
        listItemSpan.innerHTML = html.escape(name);

        return listItemSpan;
    }
}