<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\OrderProductAndShop;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'OrderProductAndShopComponent',
    template: 'Components/Controls/OrderProductAndShop/OrderProductAndShopComponent.htmL.twig'
)]
class OrderProductAndShopComponent extends TwigComponent
{
    public readonly OrderProductAndShopComponentLangDto $lang;
    public OrderProductAndShopComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'OrderProductAndShopComponent';
    }

    public function mount(OrderProductAndShopComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->lang = (new OrderProductAndShopComponentLangDto())
            ->product(
                $this->translate('product.label'),
                $this->translate('product.placeholder'),
                $this->translate('product.msg_error'),
            )
            ->productSelectButton(
                $this->translate('product_button_select.label'),
                $this->translate('product_button_select.title'),
                $this->translate('product_button_select.alt'),
            )
            ->shop(
                $this->translate('shop.label'),
                $this->translate('shop.msg_error'),
            )
            ->build();
    }
}
