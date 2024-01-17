<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\PaginatorContentLoaderJs;

use App\Twig\Components\Controls\ContentLoaderJs\ContentLoaderJsComponentDto;
use App\Twig\Components\PaginatorJs\PaginatorJsComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class PaginatorContentLoaderJsComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly ContentLoaderJsComponentDto $contentLoaderJsDto,
        public readonly PaginatorJsComponentDto $paginatorJsDto,
    ) {
    }
}
