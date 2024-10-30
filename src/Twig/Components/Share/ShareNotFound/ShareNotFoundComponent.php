<?php

declare(strict_types=1);

namespace App\Twig\Components\Share\ShareNotFound;

use App\Twig\Components\TwigComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShareNotFoundComponent',
    template: 'Components/Share/ShareNotFound/ShareNotFoundComponent.html.twig'
)]
class ShareNotFoundComponent extends TwigComponent
{
    public static function getComponentName(): string
    {
        return 'ShareNotFoundComponent';
    }
}
