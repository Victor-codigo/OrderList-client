<?php

namespace App\Twig\Components\Shop\ShopList\List;

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

    public static function getComponentName(): string
    {
        return 'ShopListComponent';
    }

    public function mount(ShopListComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();

        $this->shopDto = $this->createListComponentDto();
        $this->shopRemoveModalDto = $this->createShopRemoveModalDto();
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

    private function createShopRemoveModalDto(): ModalComponentDto
    {
        $shopModalDelete = new ShopRemoveComponentDto(
            [],
            $this->data->shopRemoveFormCsrfToken,
            $this->data->shopRemoveFormActionUrl,
            false
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
            $this->data->shopNoImagePath,
            $this->data->shopModifyCsrfToken,
            false,
            $this->data->shopModifyFormActionUrlPlaceholder
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
        );
    }
}
