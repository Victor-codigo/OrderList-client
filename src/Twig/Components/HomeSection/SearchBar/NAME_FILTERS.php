<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\SearchBar;

enum NAME_FILTERS: string
{
    case STARTS_WITH = 'starts_with';
    case ENDS_WITH = 'ends_with';
    case CONTAINS = 'contains';
    case EQUALS = 'equals';
}
