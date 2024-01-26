<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\ButtonLoading;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ButtonLoadingComponent',
    template: 'Components/Controls/ButtonLoading/ButtonLoadingComponent.html.twig'
)]
class ButtonLoadingComponent extends TwigComponent
{
    public ButtonLoadingComponentDto|TwigComponentDtoInterface $data;

    protected static function getComponentName(): string
    {
        return 'ButtonLoadingComponent';
    }

    public function mount(ButtonLoadingComponentDto $data): void
    {
        $this->data = $data;
    }
}
