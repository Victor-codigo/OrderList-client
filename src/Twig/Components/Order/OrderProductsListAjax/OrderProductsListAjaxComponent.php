<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderProductsListAjax;

use App\Twig\Components\HomeSection\ItemsListAjax\ItemsListAjaxComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'OrderProductsListAjaxComponent',
    template: 'Components/Order/OrderProductsListAjax/OrderProductsListAjaxComponent.html.twig'
)]
class OrderProductsListAjaxComponent extends ItemsListAjaxComponent
{
    public static function getComponentName(): string
    {
        return 'OrderProductsListAjaxComponent';
    }

    protected function loadTranslation(): void
    {
        $this->lang = new OrderProductsListAjaxComponentLangDto(
            $this->translate('title'),
            $this->translate('product_image.title'),
            $this->translate('button_back.label'),
            $this->translate('button_create_product.label'),
            $this->translate('list_empty.label'),
        );
    }
}
