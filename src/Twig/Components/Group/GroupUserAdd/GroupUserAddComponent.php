<?php

namespace App\Twig\Components\Group\GroupUserAdd;

use App\Form\Group\GroupUserAdd\GROUP_USER_ADD_FORM_ERRORS;
use App\Form\Group\GroupUserAdd\GROUP_USER_ADD_FORM_FIELDS;
use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupUserAddComponent',
    template: 'Components/Group/GroupUserAdd/GroupUserAddComponent.html.twig'
)]
final class GroupUserAddComponent extends TwigComponent
{
    public GroupUserAddComponentDtoLang $lang;
    public GroupUserAddComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $groupIdFieldName;
    public readonly string $nameFieldName;
    public readonly string $submitFieldName;

    public static function getComponentName(): string
    {
        return 'GroupUserAddComponent';
    }

    public function mount(GroupUserAddComponentDto $data): void
    {
        $this->data = $data;
        $this->formName = GROUP_USER_ADD_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', GROUP_USER_ADD_FORM_FIELDS::FORM, GROUP_USER_ADD_FORM_FIELDS::TOKEN);
        $this->groupIdFieldName = sprintf('%s[%s]', GROUP_USER_ADD_FORM_FIELDS::FORM, GROUP_USER_ADD_FORM_FIELDS::GROUP_ID);
        $this->nameFieldName = sprintf('%s[%s]', GROUP_USER_ADD_FORM_FIELDS::FORM, GROUP_USER_ADD_FORM_FIELDS::NAME);
        $this->submitFieldName = sprintf('%s[%s]', GROUP_USER_ADD_FORM_FIELDS::FORM, GROUP_USER_ADD_FORM_FIELDS::SUBMIT);

        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->lang = new GroupUserAddComponentDtoLang(
            $this->translate('title', ['group_name' => $this->data->groupName]),
            $this->translate('name.label'),
            $this->translate('name.placeholder'),
            $this->translate('name.msg_invalid'),
            $this->translate('button_group_user_add.label'),
            $this->data->validForm ? $this->loadErrorsTranslation() : null
        );
    }

    private function loadErrorsTranslation(): AlertComponentDto
    {
        $errorsLang = [];
        foreach ($this->data->errors as $field => $error) {
            $errorsLang[] = match ($field) {
                GROUP_USER_ADD_FORM_ERRORS::GROUP_ID->value => $this->translate('validation.error.group_id'),
                GROUP_USER_ADD_FORM_ERRORS::GROUP_NOT_FOUND->value => $this->translate('validation.error.group_not_found'),
                GROUP_USER_ADD_FORM_ERRORS::PERMISSIONS->value => $this->translate('validation.error.permission'),
                GROUP_USER_ADD_FORM_ERRORS::USERS_VALIDATION->value => $this->translate('validation.error.users_validation'),
                GROUP_USER_ADD_FORM_ERRORS::GROUP_USERS_EXCEEDED->value => $this->translate('validation.error.group_users_exceeded'),
                GROUP_USER_ADD_FORM_ERRORS::USERS->value => $this->translate('validation.error.users'),
                GROUP_USER_ADD_FORM_ERRORS::GROUP_ALREADY_IN_THE_GROUP->value => $this->translate('validation.error.group_already_in_the_group'),
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
