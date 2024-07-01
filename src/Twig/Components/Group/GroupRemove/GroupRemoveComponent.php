<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupRemove;

use App\Form\Group\GroupRemoveMulti\GROUP_REMOVE_MULTI_FORM_FIELDS;
use App\Form\Group\GroupRemove\GROUP_REMOVE_FORM_ERRORS;
use App\Form\Group\GroupRemove\GROUP_REMOVE_FORM_FIELDS;
use App\Twig\Components\HomeSection\ItemRemove\ItemRemoveComponent;
use App\Twig\Components\HomeSection\ItemRemove\ItemRemoveComponentDto;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupRemoveComponent',
    template: 'Components/HomeSection/ItemRemove/ItemRemoveComponent.html.twig'
)]
class GroupRemoveComponent extends ItemRemoveComponent
{
    public static function getComponentName(): string
    {
        return 'GroupRemoveComponent';
    }

    public function mount(ItemRemoveComponentDto $data): void
    {
        $this->data = $data;

        [$formName, $submitFieldName, $itemsIdFieldName, $tokenCsrfFieldName] = $this->data->removeMulti
            ? $this->createRemoveMultiData()
            : $this->createRemoveData();

        $this->initialize(self::getComponentName(), $formName, $submitFieldName, $itemsIdFieldName, $tokenCsrfFieldName);
        $this->loadTranslation();
        $this->titleDto = $this->createTitleDto();
    }

    private function createRemoveMultiData(): array
    {
        return [
            GROUP_REMOVE_MULTI_FORM_FIELDS::FORM,
            sprintf('%s[%s]', GROUP_REMOVE_MULTI_FORM_FIELDS::FORM, GROUP_REMOVE_MULTI_FORM_FIELDS::SUBMIT),
            sprintf('%s[%s][]', GROUP_REMOVE_MULTI_FORM_FIELDS::FORM, GROUP_REMOVE_MULTI_FORM_FIELDS::GROUPS_ID),
            sprintf('%s[%s]', GROUP_REMOVE_MULTI_FORM_FIELDS::FORM, GROUP_REMOVE_MULTI_FORM_FIELDS::TOKEN),
        ];
    }

    private function createRemoveData(): array
    {
        return [
            GROUP_REMOVE_FORM_FIELDS::FORM,
            sprintf('%s[%s]', GROUP_REMOVE_FORM_FIELDS::FORM, GROUP_REMOVE_FORM_FIELDS::SUBMIT),
            sprintf('%s[%s]', GROUP_REMOVE_FORM_FIELDS::FORM, GROUP_REMOVE_FORM_FIELDS::GROUPS_ID),
            sprintf('%s[%s]', GROUP_REMOVE_FORM_FIELDS::FORM, GROUP_REMOVE_FORM_FIELDS::TOKEN),
        ];
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->getComponentName());
        $this->lang = (new GroupRemoveComponentLangDto())
            ->title(
                $this->translate('title')
            )
            ->messageAdvice(
                $this->translate($this->data->removeMulti ? 'message_advice.text_multi' : 'message_advice.text')
            )
            ->itemRemoveButton(
                $this->translate('remove_button.label')
            )
            ->validationErrors(
                $this->createAlertValidationComponentDto()
            )
        ->build();
    }

    public function loadErrorsTranslation(array $errors): array
    {
        $errorsLang = [];
        foreach ($errors as $field => $error) {
            $errorsLang[] = match ($field) {
                GROUP_REMOVE_FORM_ERRORS::GROUP_ID_EMPTY->value,
                GROUP_REMOVE_FORM_ERRORS::GROUP_ID->value ,
                GROUP_REMOVE_FORM_ERRORS::INTERNAL_SERVER->value => $this->translate('validation.error.internal_server'),
                GROUP_REMOVE_FORM_ERRORS::GROUP_NOT_FOUND->value ,
                GROUP_REMOVE_FORM_ERRORS::PERMISSIONS->value => $this->translate('validation.error.permissions'),
                default => $this->translate('validation.error.internal_server')
            };
        }

        return $errorsLang;
    }

    public function loadValidationOkTranslation(): string
    {
        return $this->translate('validation.ok');
    }
}
