<?php

declare(strict_types=1);

namespace App\Twig\Components\Modal;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ModalComponent',
    template: 'Components/Modal/ModalComponent.html.twig'
)]
class ModalComponent extends TwigComponent
{
    public ModalComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'ModalComponent';
    }

    public function mount(ModalComponentDto $data): void
    {
        $this->data = $data;
    }
}
