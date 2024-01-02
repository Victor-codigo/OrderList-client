<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopRemove;

use App\Form\Shop\ShopRemoveMulti\SHOP_REMOVE_MULTI_FORM_FIELDS;
use App\Form\Shop\ShopRemove\SHOP_REMOVE_FORM_FIELDS;
use App\Twig\Components\HomeSection\ItemRemove\ItemRemoveComponent;
use App\Twig\Components\HomeSection\ItemRemove\ItemRemoveComponentDto;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopRemoveComponent',
    // template: 'Components/Shop/ShopRemove/ShopRemoveComponent.html.twig'
    template: 'Components/HomeSection/ItemRemove/ItemRemoveComponent.html.twig'
)]
class ShopRemoveComponent extends ItemRemoveComponent
{
    public static function getComponentName(): string
    {
        return 'ShopRemoveComponent';
    }

    public function __construct(RequestStack $request, TranslatorInterface $translator)
    {
        parent::__construct($request, $translator);
    }

    public function mount(ItemRemoveComponentDto $data): void
    {
        $this->data = $data;

        [$formName, $submitFieldName, $shopsIdFieldName, $tokenCsrfFieldName] = $this->data->removeMulti
            ? $this->createRemoveMultiData()
            : $this->createRemoveData();

        $this->initialize(self::getComponentName(), $formName, $submitFieldName, $shopsIdFieldName, $tokenCsrfFieldName);
        $this->loadTranslation();
        $this->titleDto = $this->createTitleDto();
    }

    private function createRemoveMultiData(): array
    {
        return [
            SHOP_REMOVE_MULTI_FORM_FIELDS::FORM,
             sprintf('%s[%s]', SHOP_REMOVE_MULTI_FORM_FIELDS::FORM, SHOP_REMOVE_MULTI_FORM_FIELDS::SUBMIT),
             sprintf('%s[%s][]', SHOP_REMOVE_MULTI_FORM_FIELDS::FORM, SHOP_REMOVE_MULTI_FORM_FIELDS::SHOPS_ID),
             sprintf('%s[%s]', SHOP_REMOVE_MULTI_FORM_FIELDS::FORM, SHOP_REMOVE_MULTI_FORM_FIELDS::TOKEN),
        ];
    }

    private function createRemoveData(): array
    {
        return [
             SHOP_REMOVE_FORM_FIELDS::FORM,
             sprintf('%s[%s]', SHOP_REMOVE_FORM_FIELDS::FORM, SHOP_REMOVE_FORM_FIELDS::SUBMIT),
             sprintf('%s[%s]', SHOP_REMOVE_FORM_FIELDS::FORM, SHOP_REMOVE_FORM_FIELDS::SHOPS_ID),
             sprintf('%s[%s]', SHOP_REMOVE_FORM_FIELDS::FORM, SHOP_REMOVE_FORM_FIELDS::TOKEN),
        ];
    }

    protected function loadTranslation(): void
    {
        $this->lang = (new ShopRemoveComponentLangDto())
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
