<?php

declare(strict_types=1);

namespace App\Twig\Components\Paginator;

use App\Twig\Components\TwigComponentDtoInterface;

class PaginatorComponentDto implements TwigComponentDtoInterface
{
    /**
     * @param string $pageUrl url with a placeholder {pageNum} where the page number is set
     */
    public function __construct(
        public readonly int $pageCurrent,
        public readonly int $pagesTotal,
        public readonly string $pageUrl,
    ) {
    }
}
