<?php

declare(strict_types=1);

namespace App\Twig\Components\PaginatorJs;

use App\Twig\Components\TwigComponentDtoInterface;

class PaginatorJsComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly int $pageCurrent,
        public readonly int $pagesTotal,
    ) {
    }
}
