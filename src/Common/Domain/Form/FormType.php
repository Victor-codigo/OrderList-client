<?php

declare(strict_types=1);

namespace Common\Domain\Form;

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

    protected function addField(string $name, FIELD_TYPE $type, mixed $default = null, array $options = []): static
    {
        $this->formFields[] = new FormField($name, $type, $default, $options);

        return $this;
    }
}
