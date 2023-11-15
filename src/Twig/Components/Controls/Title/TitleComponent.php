<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\Title;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'TitleComponent',
    template: 'Components/Controls/Title/TitleComponent.html.twig'
)]
class TitleComponent extends TwigComponent
{
    public TitleComponentDto|TwigComponentDtoInterface $data;

    protected static function getComponentName(): string
    {
        return 'TitleComponent';
    }

    public function mount(TitleComponentDto $data): void
    {
        $this->data = $data;
    }
}
