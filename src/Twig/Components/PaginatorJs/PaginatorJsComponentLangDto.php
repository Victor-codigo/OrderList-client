<?php

declare(strict_types=1);

namespace App\Twig\Components\PaginatorJs;

class PaginatorJsComponentLangDto
{
    public function __construct(
        public readonly string $previous,
        public readonly string $next,
    ) {
    }
}
