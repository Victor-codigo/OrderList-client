<?php

namespace App\Twig\Components\Group\GroupCreate;

use App\Form\Group\GroupCreate\GROUP_CREATE_FORM_ERRORS;
use App\Form\Group\GroupCreate\GROUP_CREATE_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\DropZone\DropZoneComponentDto;
use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupCreateComponent',
    template: 'Components/Group/GroupCreate/GroupCreateComponent.html.twig'
)]
final class GroupCreateComponent extends TwigComponent
{
    public GroupCreateComponentLangDto $lang;
    public GroupCreateComponentDataDto|TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $nameFieldName;
    public readonly string $descriptionFieldName;
    public readonly string $imageFieldName;
    public readonly string $submitFieldName;

    public static function getComponentName(): string
    {
        return 'GroupCreateComponent';
    }

    public function mount(GroupCreateComponentDto $data): void
    {
        $this->formName = GROUP_CREATE_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', GROUP_CREATE_FORM_FIELDS::FORM, GROUP_CREATE_FORM_FIELDS::TOKEN);
        $this->nameFieldName = sprintf('%s[%s]', GROUP_CREATE_FORM_FIELDS::FORM, GROUP_CREATE_FORM_FIELDS::NAME);
        $this->descriptionFieldName = sprintf('%s[%s]', GROUP_CREATE_FORM_FIELDS::FORM, GROUP_CREATE_FORM_FIELDS::DESCRIPTION);
        $this->imageFieldName = sprintf('%s[%s]', GROUP_CREATE_FORM_FIELDS::FORM, GROUP_CREATE_FORM_FIELDS::IMAGE);
        $this->submitFieldName = sprintf('%s[%s]', GROUP_CREATE_FORM_FIELDS::FORM, GROUP_CREATE_FORM_FIELDS::SUBMIT);

        $this->data = new GroupCreateComponentDataDto(
            $data,
            $this->getDopZoneDto()
        );

        $this->loadTranslation();
        $this->titleDto = $this->createTitleComponent();
    }

    private function createTitleComponent(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title, TITLE_TYPE::POP_UP);
    }

    private function getDopZoneDto(): DropZoneComponentDto
    {
        return new DropZoneComponentDto(
            GROUP_CREATE_FORM_FIELDS::IMAGE,
            GROUP_CREATE_FORM_FIELDS::FORM,
            $this->translate('image.label'),
            GROUP_CREATE_FORM_FIELDS::IMAGE,
            $this->translate('image.placeholder')
        );
    }

    private function loadTranslation(): void
    {
        $this->lang = (new GroupCreateComponentLangDto())
        ->title(
            $this->translate('title'),
        )
        ->name(
            $this->translate('name.label'),
            $this->translate('name.placeholder'),
            $this->translate('name.msg_invalid'),
        )
        ->description(
            $this->translate('description.label'),
            $this->translate('description.placeholder'),
            $this->translate('description.msg_invalid'),
        )
        ->createButton(
            $this->translate('button_group_create.label'),
        )
        ->errors(
            $this->data->groupCreate->validForm ? $this->createAlertValidationComponentDto() : null
        )
        ->build();
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
                GROUP_CREATE_FORM_ERRORS::NAME->value => $this->translate('validation.error.name'),
                GROUP_CREATE_FORM_ERRORS::DESCRIPTION->value => $this->translate('validation.error.description'),
                GROUP_CREATE_FORM_ERRORS::GROUP_NAME_REPEATED->value => $this->translate('validation.error.name_repeated'),
                GROUP_CREATE_FORM_ERRORS::IMAGE->value => $this->translate('validation.error.image'),
                default => $this->translate('validation.error.internal_server')
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
        $errorsLang = $this->loadErrorsTranslation($this->data->groupCreate->errors);

        return new AlertValidationComponentDto(
            array_unique([$this->loadValidationOkTranslation()]),
            array_unique($errorsLang)
        );
    }
}
