import HomeListItemController from 'App/Twig/Components/HomeSection/HomeList/ListItem/HomeListItem_controller';
import * as endpoint from 'App/modules/ApiEndpoints';

export default class extends HomeListItemController {
    /**
     * @param {boolean} admin
     */
    #toggleGrantsButtons(admin) {
        const buttonGrantsUpgradeTag = this.element.querySelector('[data-js-button-grants-upgrade]');
        const buttonGrantsDowngradeTag = this.element.querySelector('[data-js-button-grants-downgrade]');

        if (admin) {
            buttonGrantsUpgradeTag.hidden = true;
            buttonGrantsDowngradeTag.hidden = false;
        } else {
            buttonGrantsUpgradeTag.hidden = false;
            buttonGrantsDowngradeTag.hidden = true;
        }
    }

    /**
     * @param {boolean} admin
     */
    #toggleAdminBadge(admin) {
        const adminBadgeTag = this.element.querySelector('[data-js-admin-badge]');

        if (admin) {
            adminBadgeTag.hidden = false;
        } else {
            adminBadgeTag.hidden = true;
        }
    }

    #userIsLastAdmin() {
        const buttonGrantsDowngradeTag = this.element.querySelector('[data-js-button-grants-downgrade]');

        alert(buttonGrantsDowngradeTag.dataset.msgErrorLastAdmin);
    }

    /**
     * @param {boolean} admin
     */
    async #groupUserChangeGrants(admin) {
        const groupUsersTag = this.element.closest('[data-controller="GroupUsersHomeSectionComponent"]');
        const userData = this.getItemData();

        try {
            await endpoint.groupUserChangeRole(groupUsersTag.dataset.groupId, [userData.id], admin);
            this.#toggleGrantsButtons(admin);
            this.#toggleAdminBadge(admin);
        } catch (Error) {
            this.#userIsLastAdmin();
        }
    }

    handleGrantsUpgradeToParent() {
        this.#groupUserChangeGrants(true);
    }

    handleGrantsDowngradeToParent() {
        this.#groupUserChangeGrants(false);
    }
}