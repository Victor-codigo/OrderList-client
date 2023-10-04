<?php

declare(strict_types=1);

namespace Common\Domain\Form;

class FormField
{
    public function __construct(
        public readonly string $name,
        public readonly FIELD_TYPE $type,
        public readonly mixed $default
    ) {
    }
}
