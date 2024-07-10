<?php

declare(strict_types=1);

namespace App\Twig\Components\Common\Legal\Privacy;

use App\Twig\Components\TwigComponentDtoInterface;

class PrivacyComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $domainName,
        public readonly string $adminEmail,
    ) {
    }
}
