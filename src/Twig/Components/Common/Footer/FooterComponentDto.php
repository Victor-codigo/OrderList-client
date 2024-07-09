<?php

declare(strict_types=1);

namespace App\Twig\Components\Common\Footer;

use App\Twig\Components\TwigComponentDtoInterface;

class FooterComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $domainName
    ) {
    }
}
