<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductHome\Home;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ProductHomeSectionComponent',
    template: 'Components/Product/ProductHome/Home/ProductHomeSectionComponent.html.twig'
)]
class ProductHomeSectionComponent extends TwigComponent
{
    public ProductHomeSectionComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'ProductHomeSectionComponent';
    }

    public function mount(ProductHomeSectionComponentDto $data): void
    {
        $this->data = $data;
    }
}
