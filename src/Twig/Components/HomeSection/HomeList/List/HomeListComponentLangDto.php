<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\HomeList\List;

class HomeListComponentLangDto
{
    public function __construct(
        public readonly string $homeListEmptyMessage,
        public readonly string $homeListEmptyIconAlt,
    ) {
    }
}
