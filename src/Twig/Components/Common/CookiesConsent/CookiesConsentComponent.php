<?php

declare(strict_types=1);

namespace App\Twig\Components\Common\CookiesConsent;

use App\Twig\Components\TwigComponent;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'CookiesConsentComponent',
    template: 'Components/Common/CookiesConsent/CookiesConsentComponent.html.twig'
)]
class CookiesConsentComponent extends TwigComponent
{
    protected static function getComponentName(): string
    {
        return 'CookiesConsentComponent';
    }
}
