<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\ContentLoaderJs;

use App\Twig\Components\TwigComponentDtoInterface;

class ContentLoaderJsComponentDto implements TwigComponentDtoInterface
{
    /**
     * @param string[] $queryParameters
     */
    public function __construct(
        public readonly string $endpointName,
        public readonly array $queryParameters,
        public readonly string $responseIndexName,
    ) {
    }
}
