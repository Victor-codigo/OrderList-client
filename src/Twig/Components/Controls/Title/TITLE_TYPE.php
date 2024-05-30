<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\Title;

enum TITLE_TYPE: string
{
    case PAGE_MAIN = 'page_main';
    case POP_UP = 'pop_up';
}
