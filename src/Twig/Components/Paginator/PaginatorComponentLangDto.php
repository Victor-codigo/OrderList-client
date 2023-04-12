<?php

declare(strict_types=1);

namespace App\Twig\Components\Paginator;

class PaginatorComponentLangDto
{
    public function __construct(
        public readonly string $previous,
        public readonly string $next,
    ) {
    }
}
