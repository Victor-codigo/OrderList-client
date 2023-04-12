<?php

namespace App\Twig\Components\Group\GroupList\List;

use App\Form\Group\GroupList\GROUP_LIST_FORM_FIELDS;
use App\Form\Group\GroupRemove\GROUP_REMOVE_FORM_ERRORS;
use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use App\Twig\Components\List\ListComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupListComponent',
    template: 'Components/Group/GroupList/List/GroupListComponent.html.twig'
)]
final class GroupListComponent extends TwigComponent
{
    private const API_DOMAIN = HTTP_CLIENT_CONFIGURATION::API_DOMAIN;
    public GroupListComponentLangDto $lang;
    public GroupListComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $submitFieldName;
    public readonly ListComponentDto $listDto;

    public static function getComponentName(): string
    {
        return 'GroupListComponent';
    }

    public function mount(GroupListComponentDto $data): void
    {
        $this->data = $data;

        $this->formName = GROUP_LIST_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', GROUP_LIST_FORM_FIELDS::FORM, GROUP_LIST_FORM_FIELDS::TOKEN);
        $this->submitFieldName = sprintf('%s[%s]', GROUP_LIST_FORM_FIELDS::FORM, GROUP_LIST_FORM_FIELDS::SUBMIT_REMOVE);

        $this->loadTranslation();

        $this->listDto = new ListComponentDto(
            'GroupListItemComponent',
            $this->data->groupList,
            self::API_DOMAIN.'/assets/img/common/list-icon.svg',
            $this->lang->listEmptyIconAlt,
            $this->lang->listEmptyMessage
        );
    }

    private function loadTranslation(): void
    {
        $this->lang = new GroupListComponentLangDto(
            $this->translate('title'),
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
                GROUP_REMOVE_FORM_ERRORS::GROUP_ID_WRONG->value => $this->translate('validation.error.group_id_wrong'),
                GROUP_REMOVE_FORM_ERRORS::GROUP_NOT_FOUND->value => $this->translate('validation.error.group_not_found'),
                GROUP_REMOVE_FORM_ERRORS::PERMISSIONS->value => $this->translate('validation.error.group_permissions'),
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
