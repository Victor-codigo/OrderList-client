<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\SearchBar;

use App\Twig\Components\TwigComponentDtoInterface;

class SearchBarComponentDto implements TwigComponentDtoInterface
{
    /**
     * @param SECTION_FILTERS[] $sectionFilters
     */
    public function __construct(
        public readonly string $groupId,
        public readonly string|null $searchValue,
        public readonly array $sectionFilters,
        public readonly string|null $sectionFilterValue,
        public readonly string|null $nameFilterValue,
        public readonly string $searchCsrfToken,
        public readonly string $searchFormActionUrl,
        public readonly string $searchAutoCompleteUrl,
    ) {
    }
}
