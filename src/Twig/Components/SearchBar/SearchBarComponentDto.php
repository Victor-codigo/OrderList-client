<?php

declare(strict_types=1);

namespace App\Twig\Components\SearchBar;

use App\Twig\Components\TwigComponentDtoInterface;

class SearchBarComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string|null $searchFilter,
        public readonly string|null $searchValue,
        public readonly string $searchCsrfToken,
        public readonly string $searchFormActionUrl,
    ) {
    }
}
