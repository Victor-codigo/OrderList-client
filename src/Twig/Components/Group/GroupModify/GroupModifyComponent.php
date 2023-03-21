<?php

namespace App\Twig\Components\Group\GroupModify;

use App\Form\Group\GroupModify\GROUP_MODIFY_FORM_ERRORS;
use App\Form\Group\GroupModify\GROUP_MODIFY_FORM_FIELDS;
use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use App\Twig\Components\Controls\DropZone\DropZoneComponentDto;
use App\Twig\Components\Controls\ImageAvatar\ImageAvatarComponentDto;
use App\Twig\Components\Group\GroupRemove\GroupRemoveComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupModifyComponent',
    template: 'Components/Group/GroupModify/GroupModifyComponent.html.twig'
)]
final class GroupModifyComponent extends TwigComponent
{
    public GroupModifyComponentDtoLang $lang;
    public GroupModifyComponentDataDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $groupIdFieldName;
    public readonly string $nameFieldName;
    public readonly string $descriptionFieldName;
    public readonly string $imageFieldName;
    public readonly string $imageAvatarRemoveFieldName;
    public readonly string $submitFieldName;

    public static function getComponentName(): string
    {
        return 'GroupModifyComponent';
    }

    public function mount(GroupModifyComponentDto $data): void
    {
        $this->formName = GROUP_MODIFY_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', GROUP_MODIFY_FORM_FIELDS::FORM, GROUP_MODIFY_FORM_FIELDS::TOKEN);
        $this->groupIdFieldName = sprintf('%s[%s]', GROUP_MODIFY_FORM_FIELDS::FORM, GROUP_MODIFY_FORM_FIELDS::GROUP_ID);
        $this->nameFieldName = sprintf('%s[%s]', GROUP_MODIFY_FORM_FIELDS::FORM, GROUP_MODIFY_FORM_FIELDS::NAME);
        $this->descriptionFieldName = sprintf('%s[%s]', GROUP_MODIFY_FORM_FIELDS::FORM, GROUP_MODIFY_FORM_FIELDS::DESCRIPTION);
        $this->imageFieldName = sprintf('%s[%s]', GROUP_MODIFY_FORM_FIELDS::FORM, GROUP_MODIFY_FORM_FIELDS::IMAGE);
        $this->imageAvatarRemoveFieldName = sprintf('%s[%s]', GROUP_MODIFY_FORM_FIELDS::FORM, GROUP_MODIFY_FORM_FIELDS::IMAGE_REMOVE);
        $this->submitFieldName = sprintf('%s[%s]', GROUP_MODIFY_FORM_FIELDS::FORM, GROUP_MODIFY_FORM_FIELDS::SUBMIT);

        $this->data = new GroupModifyComponentDataDto(
            $data,
            $this->getGroupRemoveDto($data),
            $this->getDopZoneDto(),
            $this->getImageAvatarDto($data)
        );
        $this->loadTranslation();
    }

    private function getDopZoneDto(): DropZoneComponentDto
    {
        return new DropZoneComponentDto(
            GROUP_MODIFY_FORM_FIELDS::IMAGE,
            GROUP_MODIFY_FORM_FIELDS::FORM,
            $this->translate('image.label'),
            GROUP_MODIFY_FORM_FIELDS::IMAGE,
            $this->translate('image.placeholder')
        );
    }

    private function getImageAvatarDto(GroupModifyComponentDto $data): ImageAvatarComponentDto
    {
        return new ImageAvatarComponentDto(
            $data->image,
            $data->imageNoAvatar,
            $this->translate('image_thumbnail.alt')
        );
    }

    private function getGroupRemoveDto(GroupModifyComponentDto $data): ModalComponentDto
    {
        $groupRemoveDto = new GroupRemoveComponentDto(
            [],
            $data->groupId,
            $data->groupRemoveCsrfToken
        );

        return new ModalComponentDto(
            'group_remove_modal',
            '',
            false,
            'GroupRemoveComponent',
            $groupRemoveDto,
            []
        );
    }

    private function loadTranslation(): void
    {
        $this->lang = new GroupModifyComponentDtoLang(
            $this->translate('title'),
            $this->translate('name.label'),
            $this->translate('name.placeholder'),
            $this->translate('name.msg_invalid'),
            $this->translate('description.label'),
            $this->translate('description.placeholder'),
            $this->translate('description.msg_invalid'),
            $this->translate('image_thumbnail.alt'),
            $this->translate('button_group_modify.label'),
            $this->translate('group_remove.label'),
            $this->translate('group_remove.placeholder'),
            $this->data->groupModify->validForm ? $this->loadErrorsTranslation() : null
        );
    }

    private function loadErrorsTranslation(): AlertComponentDto
    {
        $errorsLang = [];
        foreach ($this->data->groupModify->errors as $field => $error) {
            $errorsLang[] = match ($field) {
                GROUP_MODIFY_FORM_ERRORS::GROUP_ID->value,
                GROUP_MODIFY_FORM_ERRORS::GROUP_NOT_FOUND->value => $this->translate('validation.error.group_not_found'),
                GROUP_MODIFY_FORM_ERRORS::NAME->value => $this->translate('validation.error.name'),
                GROUP_MODIFY_FORM_ERRORS::DESCRIPTION->value => $this->translate('validation.error.description'),
                GROUP_MODIFY_FORM_ERRORS::IMAGE->value => $this->translate('validation.error.image'),
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
