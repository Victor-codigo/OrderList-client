<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductRemove;

use App\Form\Product\ProductRemoveMulti\PRODUCT_REMOVE_MULTI_FORM_FIELDS;
use App\Form\Product\ProductRemove\PRODUCT_REMOVE_FORM_FIELDS;
use App\Twig\Components\HomeSection\ItemRemove\ItemRemoveComponent;
use App\Twig\Components\HomeSection\ItemRemove\ItemRemoveComponentDto;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ProductRemoveComponent',
    template: 'Components/HomeSection/ItemRemove/ItemRemoveComponent.html.twig'
)]
class ProductRemoveComponent extends ItemRemoveComponent
{
    public static function getComponentName(): string
    {
        return 'ProductRemoveComponent';
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
            PRODUCT_REMOVE_MULTI_FORM_FIELDS::FORM,
            sprintf('%s[%s]', PRODUCT_REMOVE_MULTI_FORM_FIELDS::FORM, PRODUCT_REMOVE_MULTI_FORM_FIELDS::SUBMIT),
            sprintf('%s[%s][]', PRODUCT_REMOVE_MULTI_FORM_FIELDS::FORM, PRODUCT_REMOVE_MULTI_FORM_FIELDS::PRODUCTS_ID),
            sprintf('%s[%s]', PRODUCT_REMOVE_MULTI_FORM_FIELDS::FORM, PRODUCT_REMOVE_MULTI_FORM_FIELDS::TOKEN),
        ];
    }

    private function createRemoveData(): array
    {
        return [
            PRODUCT_REMOVE_FORM_FIELDS::FORM,
            sprintf('%s[%s]', PRODUCT_REMOVE_FORM_FIELDS::FORM, PRODUCT_REMOVE_FORM_FIELDS::SUBMIT),
            sprintf('%s[%s]', PRODUCT_REMOVE_FORM_FIELDS::FORM, PRODUCT_REMOVE_FORM_FIELDS::PRODUCTS_ID),
            sprintf('%s[%s]', PRODUCT_REMOVE_FORM_FIELDS::FORM, PRODUCT_REMOVE_FORM_FIELDS::TOKEN),
        ];
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->getComponentName());
        $this->lang = (new ProductRemoveComponentLangDto())
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
