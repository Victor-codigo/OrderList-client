<?php

declare(strict_types=1);

namespace App\Twig\Components\GroupUsers\GroupUsersAdd;

use App\Form\GroupUsers\GroupUsersAdd\GROUP_USERS_ADD_FORM_ERRORS;
use App\Form\GroupUsers\GroupUsersAdd\GROUP_USERS_ADD_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupUsersAddComponent',
    template: 'Components/GroupUsers/GroupUsersAdd/GroupUsersAddComponent.html.twig'
)]
final class GroupUsersAddComponent extends TwigComponent
{
    public GroupUsersAddComponentDtoLang $lang;
    public GroupUsersAddComponentDto|TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $groupIdFieldName;
    public readonly string $nameFieldName;
    public readonly string $submitFieldName;

    public static function getComponentName(): string
    {
        return 'GroupUsersAddComponent';
    }

    public function mount(GroupUsersAddComponentDto $data): void
    {
        $this->data = $data;
        $this->formName = GROUP_USERS_ADD_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', GROUP_USERS_ADD_FORM_FIELDS::FORM, GROUP_USERS_ADD_FORM_FIELDS::TOKEN);
        $this->groupIdFieldName = sprintf('%s[%s]', GROUP_USERS_ADD_FORM_FIELDS::FORM, GROUP_USERS_ADD_FORM_FIELDS::GROUP_ID);
        $this->nameFieldName = sprintf('%s[%s]', GROUP_USERS_ADD_FORM_FIELDS::FORM, GROUP_USERS_ADD_FORM_FIELDS::NAME);
        $this->submitFieldName = sprintf('%s[%s]', GROUP_USERS_ADD_FORM_FIELDS::FORM, GROUP_USERS_ADD_FORM_FIELDS::SUBMIT);

        $this->loadTranslation();
        $this->titleDto = $this->createTitle();
    }

    private function createTitle(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title, TITLE_TYPE::POP_UP, null);
    }

    private function loadTranslation(): void
    {
        $this->lang = new GroupUsersAddComponentDtoLang(
            $this->translate('title', ['group_name' => $this->data->groupName]),
            $this->translate('name.label'),
            $this->translate('name.placeholder'),
            $this->translate('name.msg_invalid'),
            $this->translate('button_group_user_add.label'),
            $this->translate('button_group_close.label'),
            $this->data->validForm ? $this->createAlertValidationComponentDto() : null
        );
    }

    /**
     * @param string[] $errors
     *
     * @return string[]
     */
    public function loadErrorsTranslation(array $errors): array
    {
        $errorsLang = [];
        foreach ($errors as $field => $error) {
            $errorsLang[] = match ($field) {
                GROUP_USERS_ADD_FORM_ERRORS::GROUP_ID->value => $this->translate('validation.error.group_id'),
                GROUP_USERS_ADD_FORM_ERRORS::GROUP_NOT_FOUND->value => $this->translate('validation.error.group_not_found'),
                GROUP_USERS_ADD_FORM_ERRORS::PERMISSIONS->value => $this->translate('validation.error.permission'),
                GROUP_USERS_ADD_FORM_ERRORS::USERS_VALIDATION->value => $this->translate('validation.error.users_validation'),
                GROUP_USERS_ADD_FORM_ERRORS::GROUP_USERS_EXCEEDED->value => $this->translate('validation.error.group_users_exceeded'),
                GROUP_USERS_ADD_FORM_ERRORS::USERS->value => $this->translate('validation.error.users'),
                GROUP_USERS_ADD_FORM_ERRORS::GROUP_ALREADY_IN_THE_GROUP->value => $this->translate('validation.error.group_already_in_the_group'),
                GROUP_USERS_ADD_FORM_ERRORS::TRYOUT_ROUTE_PERMISSIONS->value => $this->translate('validation.error.tryout_route_permissions'),
                default => $this->translate('validation.error.internal_server'),
            };
        }

        return $errorsLang;
    }

    public function loadValidationOkTranslation(): string
    {
        return $this->translate('validation.ok');
    }

    private function createAlertValidationComponentDto(): AlertValidationComponentDto
    {
        $errorsLang = $this->loadErrorsTranslation($this->data->errors);

        return new AlertValidationComponentDto(
            array_unique([$this->loadValidationOkTranslation()]),
            array_unique($errorsLang)
        );
    }
}
