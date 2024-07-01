<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductsListAjax;

use App\Twig\Components\HomeSection\ItemsListAjax\ItemsListAjaxComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ProductsListAjaxComponent',
    template: 'Components/Product/ProductsListAjax/ProductsListAjaxComponent.html.twig'
)]
class ProductsListAjaxComponent extends ItemsListAjaxComponent
{
    public static function getComponentName(): string
    {
        return 'ProductsListAjaxComponent';
    }

    protected function loadTranslation(): void
    {
        $this->lang = new ProductsListAjaxComponentLangDto(
            $this->translate('title'),
            $this->translate('product_image.title'),
            $this->translate('button_back.label'),
            $this->translate('button_create_product.label'),
            $this->translate('list_empty.label'),
        );
    }
}
