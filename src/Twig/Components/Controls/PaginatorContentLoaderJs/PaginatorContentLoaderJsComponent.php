<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\PaginatorContentLoaderJs;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'PaginatorContentLoaderJsComponent',
    template: 'Components/Controls/PaginatorContentLoaderJs/PaginatorContentLoaderJsComponent.html.twig'
)]
class PaginatorContentLoaderJsComponent extends TwigComponent
{
    public PaginatorContentLoaderJsComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'PaginatorContentLoaderJsComponent';
    }

    public function mount(PaginatorContentLoaderJsComponentDto $data): void
    {
        $this->data = $data;
    }
}
