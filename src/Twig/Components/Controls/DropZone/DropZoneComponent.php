<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\DropZone;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'DropZoneComponent',
    template: 'Components/Controls/DropZone/DropZoneComponent.html.twig'
)]
class DropZoneComponent extends TwigComponent
{
    public DropZoneComponentDto|TwigComponentDtoInterface $data;

    protected static function getComponentName(): string
    {
        return 'DropZoneComponent';
    }

    public function mount(DropZoneComponentDto $data): void
    {
        $this->data = $data;
    }
}
