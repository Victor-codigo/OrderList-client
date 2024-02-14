<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductInfo;

use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponent;
use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentDto;
use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentLangDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ProductInfoComponent',
    template: 'Components/HomeSection/ItemInfo/ItemInfoComponent.html.twig'
)]
class ProductInfoComponent extends ItemInfoComponent
{
    public readonly ItemInfoComponentLangDto $lang;
    public ProductInfoComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'ProductInfoComponent';
    }

    public function mount(ItemInfoComponentDto $data): void
    {
        $this->data = $data;
        $this->componentName = self::getComponentName();
        $this->loadTranslation();
    }

    protected function loadTranslation(): void
    {
        $this->lang = (new ProductInfoComponentLangDto())
            ->info(
                $this->translate('image.title'),
                $this->translate('image.alt'),
                $this->translate('created_on'),
            )
            ->priceHeaders(
                $this->translate('item_price.name'),
                $this->translate('item_price.price'),
                $this->translate('item_price.unit'),
            )
            ->shopsEmpty(
                $this->translate('shops.empty')
            )
            ->buttons(
                $this->translate('close_button.label')
            )
            ->build();
    }
}
