<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersListAjax;

use App\Twig\Components\HomeSection\ItemsListAjax\ItemsListAjaxComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ListOrdersListAjaxComponent',
    template: 'Components/ListOrders/ListOrdersListAjax/ListOrdersListAjaxComponent.html.twig'
)]
class ListOrdersListAjaxComponent extends ItemsListAjaxComponent
{
    public static function getComponentName(): string
    {
        return 'ListOrdersListAjaxComponent';
    }

    protected function loadTranslation(): void
    {
        $this->lang = new ListOrdersListAjaxComponentLangDto(
            $this->translate('title'),
            $this->translate('list_orders_image.title'),
            $this->translate('button_back.label'),
            '',
            $this->translate('list_empty.label'),
        );
    }
}
