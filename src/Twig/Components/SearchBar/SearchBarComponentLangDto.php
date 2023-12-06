<?php

declare(strict_types=1);

namespace App\Twig\Components\SearchBar;

class SearchBarComponentLangDto
{
    public readonly string $searchLabel;
    public readonly string $searchPlaceholder;
    public readonly string $searchButton;
    public readonly array $filters;

    private array $builder = [
        'searchInput' => false,
        'filters' => false,
    ];

    public function input(string $searchLabel, string $searchPlaceholder, string $searchButton): self
    {
        $this->builder['searchInput'] = true;

        $this->searchLabel = $searchLabel;
        $this->searchPlaceholder = $searchPlaceholder;
        $this->searchButton = $searchButton;

        return $this;
    }

    public function filters(array $filters): self
    {
        $this->builder['filters'] = true;

        $this->filters = $filters;

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
