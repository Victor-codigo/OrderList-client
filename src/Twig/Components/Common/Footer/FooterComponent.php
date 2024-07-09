<?php

declare(strict_types=1);

namespace App\Twig\Components\Common\Footer;

use App\Twig\Components\TwigComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'FooterComponent',
    template: 'Components/Common/Footer/FooterComponent.html.twig'
)]
class FooterComponent extends TwigComponent
{
    protected static function getComponentName(): string
    {
        return 'FooterComponent';
    }

    public function mount(): void
    {
    }
}
