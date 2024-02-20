<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\SearchBar;

enum SECTION_FILTERS: string
{
    case PRODUCT = 'product';
    case SHOP = 'shop';
    case ORDER = 'order';
}
