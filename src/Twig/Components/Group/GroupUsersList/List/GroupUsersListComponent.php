<?php

namespace App\Twig\Components\Group\GroupUsersList\List;

use App\Form\Group\GroupUserRemove\GROUP_USER_REMOVE_FORM_ERRORS;
use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use App\Twig\Components\List\ListComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupUsersListComponent_old',
    template: 'Components/Group/GroupUsersList/List/GroupUsersListComponent.html.twig'
)]
final class GroupUsersListComponent extends TwigComponent
{
    private const API_DOMAIN = HTTP_CLIENT_CONFIGURATION::API_DOMAIN;
    public GroupUsersListComponentLangDto $lang;
    public GroupUsersListComponentDto|TwigComponentDtoInterface $data;
    public readonly ListComponentDto $listDto;

    public static function getComponentName(): string
    {
        return 'GroupUsersListComponent';
    }

    public function mount(GroupUsersListComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();

        $this->listDto = new ListComponentDto(
            'GroupUsersListItemComponent',
            $this->data->groupUsersList,
            $this->lang->listEmptyIconAlt,
            $this->lang->listEmptyMessage
        );
    }

    private function loadTranslation(): void
    {
        $this->lang = new GroupUsersListComponentLangDto(
            $this->translate('title', ['group_name' => $this->data->groupName]),
            $this->translate('list_empty.message'),
            $this->translate('list_empty.icon.alt'),
            $this->data->validForm ? $this->loadErrorsTranslation() : null
        );
    }

    private function loadErrorsTranslation(): AlertComponentDto
    {
        $errorsLang = [];
        foreach ($this->data->errors as $field => $error) {
            $errorsLang[] = match ($field) {
                GROUP_USER_REMOVE_FORM_ERRORS::GROUP_ID_WRONG->value => $this->translate('validation.error.group_id_wrong'),
                GROUP_USER_REMOVE_FORM_ERRORS::GROUP_EMPTY->value => $this->translate('validation.error.group_empty'),
                GROUP_USER_REMOVE_FORM_ERRORS::GROUP_USERS_NOT_FOUND->value => $this->translate('validation.error.group_users_not_found'),
                GROUP_USER_REMOVE_FORM_ERRORS::GROUP_WITHOUT_ADMINS->value => $this->translate('validation.error.group_without_admins'),
                GROUP_USER_REMOVE_FORM_ERRORS::PERMISSIONS->value => $this->translate('validation.error.permissions'),
                GROUP_USER_REMOVE_FORM_ERRORS::USERS->value => $this->translate('validation.error.users'),
                default => $this->translate('validation.error.internal_server')
            };
        }

        if (!empty($errorsLang)) {
            return new AlertComponentDto(
                ALERT_TYPE::DANGER,
                '',
                '',
                array_unique($errorsLang)
            );
        }

        return new AlertComponentDto(
            ALERT_TYPE::SUCCESS,
            '',
            '',
            $this->translate('validation.ok')
        );
    }
}
