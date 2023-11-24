<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopRemove;

use App\Twig\Components\AlertValidation\AlertValidationComponentDto;

class ShopRemoveComponentLangDto
{
    public readonly string $title;
    public readonly string $messageAdvice;
    public readonly string $shopRemoveButton;

    public readonly AlertValidationComponentDto $validationErrors;

    private array $builder = [
        'title' => false,
        'message_advice' => false,
        'shopRemoveButton' => false,
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

    public function shopRemoveButton(string $text): static
    {
        $this->builder['shopRemoveButton'] = true;

        $this->shopRemoveButton = $text;

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
