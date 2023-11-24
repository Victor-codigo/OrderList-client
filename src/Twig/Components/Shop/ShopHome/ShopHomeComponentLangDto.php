<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopHome;

use App\Twig\Components\AlertValidation\AlertValidationComponentDto;

class ShopHomeComponentLangDto
{
    public readonly string $title;
    public readonly string $buttonShopAddLabel;
    public readonly string $buttonShopAddTitle;
    public readonly string $buttonShopRemoveMultipleLabel;
    public readonly string $buttonShopRemoveMultipleTitle;

    public readonly AlertValidationComponentDto|null $validationErrors;

    private array $builder = [
        'title' => false,
        'buttonAddShopLabel' => false,
        'buttonShopRemoveMultiple' => false,
        'errors' => false,
    ];

    public function title(string $title): self
    {
        $this->builder['title'] = true;

        $this->title = $title;

        return $this;
    }

    public function buttonShopAdd(string $label, string $title): self
    {
        $this->builder['buttonAddShopLabel'] = true;

        $this->buttonShopAddLabel = $label;
        $this->buttonShopAddTitle = $title;

        return $this;
    }

    public function buttonShopRemoveMultiple(string $label, string $title): self
    {
        $this->builder['buttonShopRemoveMultiple'] = true;

        $this->buttonShopRemoveMultipleLabel = $label;
        $this->buttonShopRemoveMultipleTitle = $title;

        return $this;
    }

    public function errors(AlertValidationComponentDto|null $validationErrors): self
    {
        $this->builder['errors'] = true;

        $this->validationErrors = $validationErrors;

        return $this;
    }

    public function build(): self
    {
        if (count(array_filter($this->builder)) < count($this->builder)) {
            $methodsMandatory = implode(', ', array_keys($this->builder));
            throw new \InvalidArgumentException("Constructors: {$methodsMandatory}. Are mandatory");
        }

        return $this;
    }
}
