<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersInfo;

use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponent;
use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentDto;
use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentLangDto;
use App\Twig\Components\Product\ProductInfo\ProductInfoComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ListOrdersInfoComponent',
    template: 'Components/ListOrders/ListOrdersInfo/ListOrdersInfoComponent.html.twig'
)]
class ListOrdersInfoComponent extends ItemInfoComponent
{
    public readonly ItemInfoComponentLangDto $lang;
    public ProductInfoComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'ListOrdersInfoComponent';
    }

    public function mount(ItemInfoComponentDto $data): void
    {
        $this->data = $data;
        $this->componentName = $data->componentName;

        $this->loadTranslation();
    }

    protected function loadTranslation(): void
    {
        $this->lang = (new ListOrdersInfoComponentLangDto())
            ->info(
                $this->translate('image.title'),
                $this->translate('image.alt'),
                $this->translate('created_on'),
                null
            )
            ->dateToBuy(
                $this->translate('date_to_buy.label'),
            )
            ->description(
                $this->translate('description.label'),
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
