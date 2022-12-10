<?php

declare(strict_types=1);

namespace Common\Domain\Form;

use Common\Domain\Form\FIELD_TYPE;
use Common\Domain\Form\FormField;
use Common\Domain\Form\FormTypeInterface;

abstract class FormType implements FormTypeInterface
{
    /**
     * @var FormField[]
     */
    protected array $formFields;

    /**
     * @var FormField[]
     */
    public function getFields(): array
    {
        return $this->formFields;
    }

    public function getFieldsValueDefaults(): array
    {
        $fieldValueDefaults = [];
        foreach ($this->formFields as $field) {
            $fieldValueDefaults[$field->name] = $field->default;
        }

        return $fieldValueDefaults;
    }

    protected function addField(string $name, FIELD_TYPE $type, mixed $default = null): static
    {
        $this->formFields[] = new FormField($name, $type, $default);

        return $this;
    }
}
