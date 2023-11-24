<?php

namespace App\Twig\Components\Shop\ShopHome;

use App\Form\Shop\ShopRemoveMulti\SHOP_REMOVE_MULTI_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponent;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponentDto;
use App\Twig\Components\Shop\ShopList\List\ShopListComponentDto;
use App\Twig\Components\Shop\ShopList\ShopListComponentBuilder;
use App\Twig\Components\Shop\ShopRemove\ShopRemoveComponent;
use App\Twig\Components\Shop\ShopRemove\ShopRemoveComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopHomeComponent',
    template: 'Components/Shop/ShopHome/ShopHomeComponent.html.twig'
)]
final class ShopHomeComponent extends TwigComponent
{
    private const SHOP_CREATE_MODAL_ID = 'shop_create_modal';
    private const SHOP_REMOVE_FORM_NAME = 'shop_remove_form';
    private const SHOP_REMOVE_MULTI_MODAL_ID = 'shop_remove_multi_modal';

    public ShopHomeComponentLangDto $lang;
    public ShopHomeComponentDto|TwigComponentDtoInterface $data;

    public readonly string $shopRemoveMultiFormName;
    public readonly string $shopRemoveMultiTokenCsrfFieldName;
    public readonly string $shopRemoveShopsIdFieldName;
    public readonly string $shopRemoveMultipleSubmit;
    public readonly string $shopRemoveMultiModalIdAttribute;

    public readonly TitleComponentDto $titleDto;
    public readonly ShopListComponentDto $shopListComponentDto;
    public readonly ModalComponentDto $shopCreateModalDto;
    public readonly ModalComponentDto $shopRemoveMultiModalDto;
    public readonly AlertValidationComponentDto $alertValidationComponentDto;

    public static function getComponentName(): string
    {
        return 'ShopHomeComponent';
    }

    public function mount(ShopHomeComponentDto $data): void
    {
        $this->shopRemoveMultiFormName = SHOP_REMOVE_MULTI_FORM_FIELDS::FORM;
        $this->shopRemoveMultiTokenCsrfFieldName = sprintf('%s[%s]', SHOP_REMOVE_MULTI_FORM_FIELDS::FORM, SHOP_REMOVE_MULTI_FORM_FIELDS::TOKEN);
        $this->shopRemoveMultipleSubmit = sprintf('%s[%s]', SHOP_REMOVE_MULTI_FORM_FIELDS::FORM, SHOP_REMOVE_MULTI_FORM_FIELDS::SUBMIT);
        $this->shopRemoveShopsIdFieldName = sprintf('%s[%s][]', SHOP_REMOVE_MULTI_FORM_FIELDS::FORM, SHOP_REMOVE_MULTI_FORM_FIELDS::SHOPS_ID);
        $this->shopRemoveMultiModalIdAttribute = self::SHOP_REMOVE_MULTI_MODAL_ID;

        $this->data = $data;
        $this->loadTranslation();
        $this->titleDto = $this->createTitleDto();
        $this->shopListComponentDto = $this->createShopListComponentDto();
        $this->shopCreateModalDto = $this->createShopCreateComponentDto();
        $this->shopRemoveMultiModalDto = $this->createShopRemoveMultiComponentDto();
        $this->alertValidationComponentDto = $this->createAlertValidationComponentDto();
    }

    private function createTitleDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title);
    }

    private function createShopListComponentDto(): ShopListComponentDto
    {
        $shopListBuilder = new ShopListComponentBuilder(
            [],
            $this->data->shopsData,
            $this->data->shopNoImagePath,
            1,
            100,
            1,
            false,
            $this->data->shopModifyCsrfToken,
            $this->data->shopRemoveFormCsrfToken,
            $this->data->shopModifyFormActionUrlPlaceholder,
            $this->data->shopRemoveFormActionUrl,
            self::SHOP_REMOVE_FORM_NAME,
            $this->data->shopNoImagePath
        );

        return $shopListBuilder->__invoke();
    }

    private function createShopCreateComponentDto(): ModalComponentDto
    {
        $shopCreateComponentDto = new ShopCreateComponentDto(
            [],
            '',
            '',
            $this->data->shopCreateFormCsrfToken,
            false,
            $this->data->shopCreateFormActionUrl
        );

        return new ModalComponentDto(
            self::SHOP_CREATE_MODAL_ID,
            '',
            false,
            ShopCreateComponent::getComponentName(),
            $shopCreateComponentDto,
            []
        );
    }

    private function createShopRemoveMultiComponentDto(): ModalComponentDto
    {
        $shopRemoveMultiComponentDto = new ShopRemoveComponentDto(
            [],
            $this->data->shopRemoveMultiFormCsrfToken,
            $this->data->shopRemoveFormActionUrl,
            true,
        );

        return new ModalComponentDto(
            self::SHOP_REMOVE_MULTI_MODAL_ID,
            '',
            false,
            ShopRemoveComponent::getComponentName(),
            $shopRemoveMultiComponentDto,
            []
        );
    }

    private function createAlertValidationComponentDto(): AlertValidationComponentDto
    {
        return new AlertValidationComponentDto(
            $this->data->shopHomeMessageValidationOk,
            $this->data->shopErrorsMessage
        );
    }

    private function loadTranslation(): void
    {
        $this->lang = (new ShopHomeComponentLangDto())
            ->title(
                $this->translate('title')
            )
            ->buttonShopAdd(
                $this->translate('shop_add.label'),
                $this->translate('shop_add.title'),
            )
            ->buttonShopRemoveMultiple(
                $this->translate('shop_remove_multiple.label'),
                $this->translate('shop_remove_multiple.title'),
            )
            ->errors(
                $this->data->validForm ? $this->createAlertValidationComponentDto() : null
            )
            ->build();
    }

    public function loadValidationOkTranslation(): string
    {
        return $this->translate('validation.ok');
    }
}
