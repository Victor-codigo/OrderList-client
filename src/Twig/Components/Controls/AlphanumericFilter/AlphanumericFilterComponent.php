<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\AlphanumericFilter;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'AlphanumericFilterComponent',
    template: 'Components/Controls/AlphanumericFilter/AlphanumericFilterComponent.html.twig'
)]
class AlphanumericFilterComponent extends TwigComponent
{
    // public AlphanumericFilterComponentDto|TwigComponentDtoInterface $data;

    public const LETTERS_FILTER = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'Ñ', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    protected static function getComponentName(): string
    {
        return 'AlphanumericFilterComponent';
    }

    public function mount(): void
    {
        // $this->data = $data;
    }
}
