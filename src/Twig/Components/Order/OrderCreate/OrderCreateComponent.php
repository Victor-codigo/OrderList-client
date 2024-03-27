<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderCreate;

use App\Form\Order\OrderCreate\ORDER_CREATE_FORM_ERRORS;
use App\Form\Order\OrderCreate\ORDER_CREATE_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\OrderProductAndShop\OrderProductAndShopComponentDto;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'OrderCreateComponent',
    template: 'Components/Order/OrderCreate/OrderCreateComponent.html.twig'
)]
final class OrderCreateComponent extends TwigComponent
{
    public OrderCreateComponentLangDto $lang;
    public OrderCreateComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $submitFieldName;
    public readonly string $descriptionFieldName;
    public readonly string $amountFieldName;
    public readonly string $productIdFieldName;
    public readonly string $shopIdFieldName;
    public readonly string $listOrdersIdFieldName;
    public readonly TitleComponentDto $titleDto;
    public readonly OrderProductAndShopComponentDto $orderProductAndShopComponentDto;

    public static function getComponentName(): string
    {
        return 'OrderCreateComponent';
    }

    public function mount(OrderCreateComponentDto $data): void
    {
        $this->formName = ORDER_CREATE_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', ORDER_CREATE_FORM_FIELDS::FORM, ORDER_CREATE_FORM_FIELDS::TOKEN);
        $this->submitFieldName = sprintf('%s[%s]', ORDER_CREATE_FORM_FIELDS::FORM, ORDER_CREATE_FORM_FIELDS::SUBMIT);
        $this->descriptionFieldName = sprintf('%s[%s]', ORDER_CREATE_FORM_FIELDS::FORM, ORDER_CREATE_FORM_FIELDS::DESCRIPTION);
        $this->amountFieldName = sprintf('%s[%s]', ORDER_CREATE_FORM_FIELDS::FORM, ORDER_CREATE_FORM_FIELDS::AMOUNT);
        $this->productIdFieldName = sprintf('%s[%s]', ORDER_CREATE_FORM_FIELDS::FORM, ORDER_CREATE_FORM_FIELDS::PRODUCT_ID);
        $this->shopIdFieldName = sprintf('%s[%s]', ORDER_CREATE_FORM_FIELDS::FORM, ORDER_CREATE_FORM_FIELDS::SHOP_ID);
        $this->listOrdersIdFieldName = sprintf('%s[%s]', ORDER_CREATE_FORM_FIELDS::FORM, ORDER_CREATE_FORM_FIELDS::LIST_ORDERS_ID);

        $this->data = $data;
        $this->loadTranslation();

        $this->titleDto = $this->createTitleComponentDto();
        $this->orderProductAndShopComponentDto = $this->createProductAndShop();
    }

    private function createTitleComponentDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title);
    }

    private function createProductAndShop(): OrderProductAndShopComponentDto
    {
        return new OrderProductAndShopComponentDto(
            $this->productIdFieldName,
            true,
            $this->shopIdFieldName,
            false
        );
    }

    private function loadTranslation(): void
    {
        $this->lang = (new OrderCreateComponentLangDto())
            ->title(
                $this->translate('title.main')
            )
            ->productAndShopTitle(
                $this->translate('title.product'),
                $this->translate('title.shop'),
            )
            ->description(
                $this->translate('description.label'),
                $this->translate('description.placeholder'),
                $this->translate('description.msg_invalid')
            )
            ->amount(
                $this->translate('amount.label'),
                $this->translate('amount.placeholder'),
                $this->translate('amount.msg_invalid')
            )
            ->buttons(
                $this->translate('order_create_button.label'),
                $this->translate('close_button.label')
            )
            ->errors(
                $this->data->validForm ? $this->createAlertValidationComponentDto() : null
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
                ORDER_CREATE_FORM_ERRORS::ORDER_PRODUCT_AND_SHOP_REPEATED->value => $this->translate('validation.error.product_and_shop_repeated'),
                ORDER_CREATE_FORM_ERRORS::GROUP_ID->value ,
                ORDER_CREATE_FORM_ERRORS::LIST_ORDERS_ID->value ,
                ORDER_CREATE_FORM_ERRORS::PRODUCT_ID->value ,
                ORDER_CREATE_FORM_ERRORS::ORDERS_EMPTY->value ,
                ORDER_CREATE_FORM_ERRORS::LIST_ORDERS_NOT_FOUND->value,
                ORDER_CREATE_FORM_ERRORS::PRODUCT_NOT_FOUND->value,
                ORDER_CREATE_FORM_ERRORS::SHOP_NOT_FOUND->value,
                ORDER_CREATE_FORM_ERRORS::GROUP_ERROR->value => $this->translate('validation.error.internal_server'),
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
        $errorsLang = $this->loadErrorsTranslation($this->data->errors);

        return new AlertValidationComponentDto(
            array_unique([$this->loadValidationOkTranslation()]),
            array_unique($errorsLang)
        );
    }
}
