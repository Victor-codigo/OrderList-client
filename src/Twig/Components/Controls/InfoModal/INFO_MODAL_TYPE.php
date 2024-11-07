<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\InfoModal;

enum INFO_MODAL_TYPE: string
{
    case WARNING = 'warning';
    case INFO = 'info';
}
