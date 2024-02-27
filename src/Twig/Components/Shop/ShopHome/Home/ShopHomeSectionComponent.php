<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopHome\Home;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopHomeSectionComponent',
    template: 'Components/Shop/ShopHome/Home/ShopHomeSectionComponent.html.twig'
)]
class ShopHomeSectionComponent extends TwigComponent
{
    public ShopHomeSectionComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'ShopHomeSectionComponent';
    }

    public function mount(ShopHomeSectionComponentDto $data): void
    {
        $this->data = $data;
    }
}
