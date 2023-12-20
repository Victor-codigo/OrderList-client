<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductRemove;

use App\Twig\Components\AlertValidation\AlertValidationComponentDto;

class ProductRemoveComponentLangDto
{
    public readonly string $title;
    public readonly string $messageAdvice;
    public readonly string $productRemoveButton;

    public readonly AlertValidationComponentDto $validationErrors;

    private array $builder = [
        'title' => false,
        'message_advice' => false,
        'productRemoveButton' => false,
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

    public function productRemoveButton(string $text): static
    {
        $this->builder['productRemoveButton'] = true;

        $this->productRemoveButton = $text;

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
