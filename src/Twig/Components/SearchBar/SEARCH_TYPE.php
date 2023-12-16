<?php

declare(strict_types=1);

namespace App\Twig\Components\SearchBar;

enum SEARCH_TYPE: string
{
    case SHOP = 'shop';
    case PRODUCT = 'product';
    case ORDER = 'order';
}
