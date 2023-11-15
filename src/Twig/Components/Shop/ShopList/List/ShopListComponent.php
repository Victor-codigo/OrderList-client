<?php

namespace App\Twig\Components\Shop\ShopList\List;

use App\Form\Shop\ShopList\SHOP_LIST_FORM_ERRORS;
use App\Form\Shop\ShopList\SHOP_LIST_FORM_FIELDS;
use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use App\Twig\Components\List\ListComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Shop\ShopList\ListItem\ShopListItemComponent;
use App\Twig\Components\Shop\ShopModify\ShopModifyComponent;
use App\Twig\Components\Shop\ShopModify\ShopModifyComponentDto;
use App\Twig\Components\Shop\ShopRemove\ShopRemoveComponent;
use App\Twig\Components\Shop\ShopRemove\ShopRemoveComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Config\Config;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopListComponent',
    template: 'Components/Shop/ShopList/List/ShopListComponent.html.twig'
)]
final class ShopListComponent extends TwigComponent
{
    private const API_DOMAIN = HTTP_CLIENT_CONFIGURATION::API_DOMAIN;
    public const SHOP_MODIFY_MODAL_ID = 'shop_modify_modal';
    public const SHOP_DELETE_MODAL_ID = 'shop_delete_modal';

    public ShopListComponentLangDto $lang;
    public ShopListComponentDto|TwigComponentDtoInterface $data;
    public readonly ListComponentDto $shopDto;
    public readonly ModalComponentDto $shopRemoveModalDto;
    public readonly ModalComponentDto $shopModifyModalDto;

    public readonly string $formName;

    public readonly string $submitMultipleFieldName;
    public readonly string $shopFieldName;
    public readonly string $tokenCsrfFieldName;

    public static function getComponentName(): string
    {
        return 'ShopListComponent';
    }

    public function mount(ShopListComponentDto $data): void
    {
        $this->formName = SHOP_LIST_FORM_FIELDS::FORM;
        $this->submitMultipleFieldName = sprintf('%s[%s]', SHOP_LIST_FORM_FIELDS::FORM, SHOP_LIST_FORM_FIELDS::SHOP_REMOVE_MULTIPLE);
        $this->shopFieldName = sprintf('%s[%s]', SHOP_LIST_FORM_FIELDS::FORM, SHOP_LIST_FORM_FIELDS::SHOP_SELECTED);
        $this->tokenCsrfFieldName = sprintf('%s[%s]', SHOP_LIST_FORM_FIELDS::FORM, SHOP_LIST_FORM_FIELDS::TOKEN);

        $this->data = $data;

        $this->loadTranslation();

        $this->shopDto = $this->createListComponentDto();
        $this->shopRemoveModalDto = $this->createShopDeleteModalDto();
        $this->shopModifyModalDto = $this->createShopModifyModalDto();
    }

    private function createListComponentDto(): ListComponentDto
    {
        return new ListComponentDto(
            ShopListItemComponent::getComponentName(),
            $this->data->shops,
            self::API_DOMAIN.'/assets/img/common/list-icon.svg',
            $this->lang->listEmptyIconAlt,
            $this->lang->listEmptyMessage
        );
    }

    private function createShopDeleteModalDto(): ModalComponentDto
    {
        $shopModalDelete = new ShopRemoveComponentDto(
            [],
            $this->data->csrfToken
        );

        return new ModalComponentDto(
            self::SHOP_DELETE_MODAL_ID,
            '',
            false,
            ShopRemoveComponent::getComponentName(),
            $shopModalDelete,
            []
        );
    }

    private function createShopModifyModalDto(): ModalComponentDto
    {
        $shopModalShopModify = new ShopModifyComponentDto(
            [],
            '{name_placeholder}',
            '{description_placeholder}',
            '{image_placeholder}',
            Config::SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
            $this->data->csrfToken,
            false
        );

        return new ModalComponentDto(
            self::SHOP_MODIFY_MODAL_ID,
            '',
            false,
            ShopModifyComponent::getComponentName(),
            $shopModalShopModify,
            []
        );
    }

    private function loadTranslation(): void
    {
        $this->lang = new ShopListComponentLangDto(
            $this->translate('order_add.label'),
            $this->translate('shop_empty.message'),
            $this->translate('shop_empty.icon.alt'),
            $this->data->validForm ? $this->loadErrorsTranslation() : null
        );
    }

    private function loadErrorsTranslation(): AlertComponentDto
    {
        // $errorsLang = [];
        // foreach ($this->data->errors as $field => $error) {
        //     $errorsLang[] = match ($field) {
        //         SHOP_LIST_FORM_ERRORS:->value ,
        //         SHOP_LIST_FORM_ERRORS::SHOP_EMPTY->value => $this->translate('validation.error.empty'),
        //         SHOP_LIST_FORM_ERRORS::SHOP_ID->value ,
        //         SHOP_LIST_FORM_ERRORS::SHOP_NOT_FOUND->value,
        //         SHOP_LIST_FORM_ERRORS::GROUP_ERROR->value => $this->translate('validation.error.internal_server'),
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
