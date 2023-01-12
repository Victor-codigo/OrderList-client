<?php

declare(strict_types=1);

namespace App\Twig\Components\Modal;

class ModalComponentButtonDto
{
    public function __construct(
        public readonly string $text = 'button',
        public readonly string $class = '',
        public readonly bool $dismiss = false,
    ) {
    }
}
