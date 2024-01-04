<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\ItemPriceAdd;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ItemPriceAddComponent',
    template: 'Components/Controls/ItemPriceAdd/ItemPriceAddComponent.htmL.twig'
)]
class ItemPriceAddComponent extends TwigComponent
{
    public ItemPriceAddComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'ItemPriceAddComponent';
    }

    public function mount(ItemPriceAddComponentDto $data): void
    {
        $this->data = $data;
    }
}
