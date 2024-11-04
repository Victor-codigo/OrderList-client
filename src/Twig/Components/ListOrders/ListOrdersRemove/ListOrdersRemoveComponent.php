<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersRemove;

use App\Form\ListOrders\ListOrdersRemoveMulti\LIST_ORDERS_REMOVE_MULTI_FORM_FIELDS;
use App\Form\ListOrders\ListOrdersRemove\LIST_ORDERS_REMOVE_FORM_FIELDS;
use App\Twig\Components\HomeSection\ItemRemove\ItemRemoveComponent;
use App\Twig\Components\HomeSection\ItemRemove\ItemRemoveComponentDto;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ListOrdersRemoveComponent',
    template: 'Components/HomeSection/ItemRemove/ItemRemoveComponent.html.twig'
)]
class ListOrdersRemoveComponent extends ItemRemoveComponent
{
    public static function getComponentName(): string
    {
        return 'ListOrdersRemoveComponent';
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
            LIST_ORDERS_REMOVE_MULTI_FORM_FIELDS::FORM,
            sprintf('%s[%s]', LIST_ORDERS_REMOVE_MULTI_FORM_FIELDS::FORM, LIST_ORDERS_REMOVE_MULTI_FORM_FIELDS::SUBMIT),
            sprintf('%s[%s][]', LIST_ORDERS_REMOVE_MULTI_FORM_FIELDS::FORM, LIST_ORDERS_REMOVE_MULTI_FORM_FIELDS::LIST_ORDERS_ID),
            sprintf('%s[%s]', LIST_ORDERS_REMOVE_MULTI_FORM_FIELDS::FORM, LIST_ORDERS_REMOVE_MULTI_FORM_FIELDS::TOKEN),
        ];
    }

    private function createRemoveData(): array
    {
        return [
            LIST_ORDERS_REMOVE_FORM_FIELDS::FORM,
            sprintf('%s[%s]', LIST_ORDERS_REMOVE_FORM_FIELDS::FORM, LIST_ORDERS_REMOVE_FORM_FIELDS::SUBMIT),
            sprintf('%s[%s]', LIST_ORDERS_REMOVE_FORM_FIELDS::FORM, LIST_ORDERS_REMOVE_FORM_FIELDS::LIST_ORDERS_ID),
            sprintf('%s[%s]', LIST_ORDERS_REMOVE_FORM_FIELDS::FORM, LIST_ORDERS_REMOVE_FORM_FIELDS::TOKEN),
        ];
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->getComponentName());
        $this->lang = (new ListOrdersRemoveComponentLangDto())
            ->title(
                $this->translate('title')
            )
            ->messageAdvice(
                $this->translate($this->data->removeMulti ? 'message_advice.text_multi' : 'message_advice.text')
            )
            ->itemRemoveButton(
                $this->translate('remove_button.label')
            )
            ->itemCloseButton(
                $this->translate('close_button.label')
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
                default => $this->translate('validation.error.internal_server'),
            };
        }

        return $errorsLang;
    }

    public function loadValidationOkTranslation(): string
    {
        return $this->translate('validation.ok');
    }
}
