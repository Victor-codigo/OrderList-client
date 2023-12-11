<?php

declare(strict_types=1);

namespace App\Form\SearchBar;

use Common\Domain\Form\FormErrorInterface;

class SEARCHBAR_FORM_FIELDS implements FormErrorInterface
{
    public const FORM = 'searchbar_form';
    public const TOKEN = 'token';
    public const SEARCH_FILTER = 'search_filter';
    public const SEARCH_VALUE = 'search_value';
    public const BUTTON = 'search';
}
