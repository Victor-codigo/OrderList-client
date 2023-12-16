<?php

declare(strict_types=1);

namespace App\Twig\Components\SearchBar;

use App\Twig\Components\TwigComponentDtoInterface;

class SearchBarComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $groupId,
        public readonly string|null $searchFilter,
        public readonly string|null $searchValue,
        private readonly SEARCH_TYPE $searchType,
        public readonly string $searchCsrfToken,
        public readonly string $searchFormActionUrl,
        public readonly string $searchAutoCompleteUrl,
    ) {
    }

    public function searchType(): string
    {
        return $this->searchType->value;
    }
}
