<?php

namespace App\Twig\Components\Shop\ShopList\ListItem;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopListItemComponent',
    template: 'Components/Shop/ShopList/ListItem/ShopListItemComponent.html.twig'
)]
final class ShopListItemComponent extends TwigComponent
{
    public const API_DOMAIN = HTTP_CLIENT_CONFIGURATION::API_DOMAIN;

    public ShopListItemComponentLangDto $lang;
    public ShopListItemComponentDto|TwigComponentDtoInterface $data;

    public readonly string $shopFieldName;

    public static function getComponentName(): string
    {
        return 'ShopListItemComponent';
    }

    public function mount(ShopListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->lang = new ShopListItemComponentLangDto(
            $this->translate('shop_image.alt'),
            $this->translate('shop_image.title'),
            $this->translate('shop_modify_button.alt'),
            $this->translate('shop_modify_button.title'),
            $this->translate('shop_remove_button.alt'),
            $this->translate('shop_remove_button.title'),
        );
    }
}
