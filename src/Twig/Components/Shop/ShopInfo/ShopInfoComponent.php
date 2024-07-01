<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopInfo;

use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponent;
use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentDto;
use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentLangDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopInfoComponent',
    template: 'Components/Shop/ShopInfo/ShopInfoComponent.html.twig'
)]
class ShopInfoComponent extends ItemInfoComponent
{
    public readonly ItemInfoComponentLangDto $lang;
    public ShopInfoComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'ShopInfoComponent';
    }

    public function mount(ItemInfoComponentDto $data): void
    {
        $this->data = $data;
        $this->componentName = self::getComponentName();
        $this->loadTranslation();
    }

    protected function loadTranslation(): void
    {
        $this->lang = (new ShopInfoComponentLangDto())
            ->info(
                $this->translate('image.title'),
                $this->translate('image.alt'),
                $this->translate('created_on'),
            )
            ->description(
                $this->translate('description.label'),
            )
            ->address(
                $this->translate('address.label'),
            )
            ->priceHeaders(
                $this->translate('item_price.name'),
                $this->translate('item_price.price'),
                $this->translate('item_price.unit'),
            )
            ->shopsEmpty(
                $this->translate('products.empty')
            )
            ->buttons(
                $this->translate('close_button.label')
            )
            ->build();
    }
}
