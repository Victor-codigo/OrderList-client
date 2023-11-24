<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopRemove;

use App\Form\Shop\ShopRemoveMulti\SHOP_REMOVE_MULTI_FORM_FIELDS;
use App\Form\Shop\ShopRemove\SHOP_REMOVE_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopRemoveComponent',
    template: 'Components/Shop/ShopRemove/ShopRemoveComponent.html.twig'
)]
class ShopRemoveComponent extends TwigComponent
{
    public ShopRemoveComponentLangDto $lang;
    public ShopRemoveComponentDto|TwigComponentDtoInterface $data;
    public TitleComponentDto $titleDto;

    public readonly string $formName;
    public readonly string $submitFieldName;
    public readonly string $shopsIdFieldName;
    public readonly string $tokenCsrfFieldName;

    public static function getComponentName(): string
    {
        return 'ShopRemoveComponent';
    }

    public function __construct(RequestStack $request, TranslatorInterface $translator)
    {
        parent::__construct($request, $translator);
    }

    public function mount(ShopRemoveComponentDto $data): void
    {
        $this->data = $data;

        if ($this->data->removeMulti) {
            $this->formName = SHOP_REMOVE_MULTI_FORM_FIELDS::FORM;
            $this->submitFieldName = sprintf('%s[%s]', SHOP_REMOVE_MULTI_FORM_FIELDS::FORM, SHOP_REMOVE_MULTI_FORM_FIELDS::SUBMIT);
            $this->shopsIdFieldName = sprintf('%s[%s][]', SHOP_REMOVE_MULTI_FORM_FIELDS::FORM, SHOP_REMOVE_MULTI_FORM_FIELDS::SHOPS_ID);
            $this->tokenCsrfFieldName = sprintf('%s[%s]', SHOP_REMOVE_MULTI_FORM_FIELDS::FORM, SHOP_REMOVE_MULTI_FORM_FIELDS::TOKEN);
        } else {
            $this->formName = SHOP_REMOVE_FORM_FIELDS::FORM;
            $this->submitFieldName = sprintf('%s[%s]', SHOP_REMOVE_FORM_FIELDS::FORM, SHOP_REMOVE_FORM_FIELDS::SUBMIT);
            $this->shopsIdFieldName = sprintf('%s[%s]', SHOP_REMOVE_FORM_FIELDS::FORM, SHOP_REMOVE_FORM_FIELDS::SHOPS_ID);
            $this->tokenCsrfFieldName = sprintf('%s[%s]', SHOP_REMOVE_FORM_FIELDS::FORM, SHOP_REMOVE_FORM_FIELDS::TOKEN);
        }

        $this->loadTranslation();

        $this->titleDto = $this->createTitleDto();
    }

    private function createTitleDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title);
    }

    private function createAlertValidationComponentDto(): AlertValidationComponentDto
    {
        return new AlertValidationComponentDto(
            [$this->loadValidationOkTranslation()],
            $this->loadErrorsTranslation($this->data->errors)
        );
    }

    private function loadTranslation(): void
    {
        $this->lang = (new ShopRemoveComponentLangDto())
            ->title(
                $this->translate('title')
            )
            ->messageAdvice(
                $this->translate($this->data->removeMulti ? 'message_advice.text_multi' : 'message_advice.text')
            )
            ->shopRemoveButton(
                $this->translate('shop_remove_button.label')
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
