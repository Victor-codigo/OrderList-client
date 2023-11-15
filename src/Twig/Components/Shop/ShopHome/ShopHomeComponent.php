<?php

namespace App\Twig\Components\Shop\ShopHome;

use App\Form\Shop\ShopHome\SHOP_HOME_FORM_ERRORS;
use App\Form\Shop\ShopHome\SHOP_HOME_FORM_FIELDS;
use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponent;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponentDto;
use App\Twig\Components\Shop\ShopList\List\ShopListComponentDto;
use App\Twig\Components\Shop\ShopList\ShopListComponentBuilder;
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

    public ShopHomeComponentLangDto $lang;
    public ShopHomeComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $shopRemoveMultipleSubmit;

    public readonly TitleComponentDto $titleDto;
    public readonly ShopListComponentDto $shopListComponentDto;
    public readonly ModalComponentDto $shopCreateModalDto;

    public static function getComponentName(): string
    {
        return 'ShopHomeComponent';
    }

    public function mount(ShopHomeComponentDto $data): void
    {
        $this->formName = SHOP_HOME_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', SHOP_HOME_FORM_FIELDS::FORM, SHOP_HOME_FORM_FIELDS::TOKEN);
        $this->shopRemoveMultipleSubmit = sprintf('%s[%s]', SHOP_HOME_FORM_FIELDS::FORM, SHOP_HOME_FORM_FIELDS::SHOP_REMOVE_MULTIPLE);

        $this->data = $data;
        $this->loadTranslation();
        $this->titleDto = $this->createTitleDto();
        $this->shopListComponentDto = $this->createShopListComponentDto();
        $this->shopCreateModalDto = $this->createShopCreateComponentDto();
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
            $this->data->csrfToken,
        );

        return $shopListBuilder->__invoke();
    }

    private function createShopCreateComponentDto(): ModalComponentDto
    {
        $shopCreateComponentDto = new ShopCreateComponentDto(
            [],
            '',
            '',
            $this->data->csrfToken,
            false
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
                $this->data->validForm ? $this->loadErrorsTranslation() : null
            )
            ->build();
    }

    private function loadErrorsTranslation(): AlertComponentDto
    {
        // $errorsLang = [];
        // foreach ($this->data->errors as $field => $error) {
        //     $errorsLang[] = match ($field) {
        //         SHOP_HOME_FORM_ERRORS::NAME->value => $this->translate('validation.error.name'),
        //         SHOP_HOME_FORM_ERRORS::SHOP_NAME_REPEATED->value => $this->translate('validation.error.shop_name_repeated'),
        //         SHOP_HOME_FORM_ERRORS::IMAGE->value => $this->translate('validation.error.image'),
        //         SHOP_HOME_FORM_ERRORS::SHOP_ID->value,
        //         SHOP_HOME_FORM_ERRORS::SHOP_NOT_FOUND->value,
        //         SHOP_HOME_FORM_ERRORS::DESCRIPTION->value,
        //         SHOP_HOME_FORM_ERRORS::GROUP_ID->value => $this->translate('validation.error.internal_server'),
        //         default => $this->translate('validation.error.internal_server')
        //     };
        // }

        // if (!empty($errorsLang)) {
        //     return new AlertComponentDto(
        //         ALERT_TYPE::DANGER,
        //         '',
        //         '',
        //         array_unique($errorsLang)
        //     );
        // }

        return new AlertComponentDto(
            ALERT_TYPE::SUCCESS,
            '',
            '',
            $this->translate('validation.ok')
        );
    }
}
