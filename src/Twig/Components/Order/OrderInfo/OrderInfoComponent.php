<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderInfo;

use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponent;
use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentDto;
use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentLangDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'OrderInfoComponent',
    template: 'Components/Order/OrderInfo/OrderInfoComponent.html.twig'
)]
class OrderInfoComponent extends ItemInfoComponent
{
    public readonly ItemInfoComponentLangDto $lang;
    public OrderInfoComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'OrderInfoComponent';
    }

    public function mount(ItemInfoComponentDto $data): void
    {
        $this->data = $data;
        $this->componentName = self::getComponentName();
        $this->loadTranslation();
    }

    protected function loadTranslation(): void
    {
        $this->lang = (new OrderInfoComponentLangDto())
            ->info(
                $this->translate('image.title'),
                $this->translate('image.alt'),
                $this->translate('created_on'),
            )
            ->description(
                $this->translate('description.label'),
            )
            ->amount(
                $this->translate('amount.title')
            )
            ->bought(
                $this->translate('bought.label'),
                $this->translate('bought.bought_icon.title'),
                $this->translate('bought.bought_icon.alt'),
                $this->translate('bought.not_bought_icon.title'),
                $this->translate('bought.not_bought_icon.alt'),
            )
            ->product(
                $this->translate('product.description.label'),
            )
            ->shop(
                $this->translate('shop.description.label'),
                $this->translate('shop.price.label'),
            )
            ->price(
                $this->translate('price_total.label'),
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
