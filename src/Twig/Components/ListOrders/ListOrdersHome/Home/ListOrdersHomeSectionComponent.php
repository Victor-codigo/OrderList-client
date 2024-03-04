<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersHome\Home;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ListOrdersHomeSectionComponent',
    template: 'Components/ListOrders/ListOrdersHome/Home/ListOrdersHomeSectionComponent.html.twig'
)]
class ListOrdersHomeSectionComponent extends TwigComponent
{
    public ListOrdersHomeSectionComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'ListOrdersHomeSectionComponent';
    }

    public function mount(ListOrdersHomeSectionComponentDto $data): void
    {
        $this->data = $data;
    }
}
