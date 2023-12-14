<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\Home;

class RemoveMultiFormDto
{
    public function __construct(
        public readonly string $formName,
        public readonly string $tokenCsrfFieldName,
        public readonly string $submitFieldName,
        public readonly string $itemsIdFieldName,
        public readonly string $modalIdAttribute
    ) {
    }
}
