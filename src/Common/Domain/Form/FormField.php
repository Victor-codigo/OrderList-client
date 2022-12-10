<?php

declare(strict_types=1);

namespace Common\Domain\Form;

class FormField
{
    public readonly string $name;
    public readonly FIELD_TYPE $type;
    public readonly mixed $default;

    public function __construct(string $name, FIELD_TYPE $type, mixed $default)
    {
        $this->name = $name;
        $this->type = $type;
        $this->default = $default;
    }
}
