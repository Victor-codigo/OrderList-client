<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\ItemRemove;

use App\Twig\Components\AlertValidation\AlertValidationComponentDto;

class ItemRemoveComponentLangDto
{
    public readonly string $title;
    public readonly string $messageAdvice;
    public readonly string $itemRemoveButton;
    public readonly string $itemCloseButtonLabel;

    public readonly AlertValidationComponentDto $validationErrors;

    private array $builder = [
        'title' => false,
        'message_advice' => false,
        'itemRemoveButton' => false,
        'itemCloseButton' => false,
        'build' => false,
    ];

    public function title(string $title): static
    {
        $this->builder['title'] = true;

        $this->title = $title;

        return $this;
    }

    public function messageAdvice(string $text): static
    {
        $this->builder['message_advice'] = true;

        $this->messageAdvice = $text;

        return $this;
    }

    public function itemRemoveButton(string $text): static
    {
        $this->builder['itemRemoveButton'] = true;

        $this->itemRemoveButton = $text;

        return $this;
    }

    public function itemCloseButton(string $label): static
    {
        $this->builder['itemCloseButton'] = true;

        $this->itemCloseButtonLabel = $label;

        return $this;
    }

    public function validationErrors(AlertValidationComponentDto $validationErrors): static
    {
        $this->builder['validationErrors'] = true;

        $this->validationErrors = $validationErrors;

        return $this;
    }

    public function build(): static
    {
        $this->builder['build'] = true;

        if (count(array_filter($this->builder)) < count($this->builder)) {
            throw new \InvalidArgumentException(sprintf('Constructors: [%s]. Are mandatory', implode(', ', $this->builder)));
        }

        return $this;
    }
}
