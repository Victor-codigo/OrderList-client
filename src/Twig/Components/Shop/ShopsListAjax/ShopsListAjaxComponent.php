<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopsListAjax;

use App\Twig\Components\HomeSection\ItemsListAjax\ItemsListAjaxComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopsListAjaxComponent',
    template: 'Components/Shop/ShopsListAjax/ShopsListAjaxComponent.html.twig'
)]
class ShopsListAjaxComponent extends ItemsListAjaxComponent
{
    public static function getComponentName(): string
    {
        return 'ShopsListAjaxComponent';
    }

    protected function loadTranslation(): void
    {
        $this->lang = new ShopsListAjaxComponentLangDto(
            $this->translate('title'),
            $this->translate('shop_image.title'),
            $this->translate('button_back.label'),
            $this->translate('button_create_shop.label'),
            $this->translate('list_empty.label'),
        );
    }
}
