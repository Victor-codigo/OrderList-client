<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\Home;

use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use Common\Domain\DtoBuilder\DtoBuilder;

class HomeSectionComponentLangDto
{
    private DtoBuilder $builder;

    public readonly string $title;
    public readonly string $buttonAddLabel;
    public readonly string $buttonAddTitle;
    public readonly string $buttonRemoveMultipleLabel;
    public readonly string $buttonRemoveMultipleTitle;

    public readonly ?AlertValidationComponentDto $validationErrors;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
            'buttonAdd',
            'buttonRemoveMultiple',
            'errors',
        ]);
    }

    public function title(string $title): self
    {
        $this->builder->setMethodStatus('title', true);

        $this->title = $title;

        return $this;
    }

    public function buttonAdd(string $label, string $title): self
    {
        $this->builder->setMethodStatus('buttonAdd', true);

        $this->buttonAddLabel = $label;
        $this->buttonAddTitle = $title;

        return $this;
    }

    public function buttonRemoveMultiple(string $label, string $title): self
    {
        $this->builder->setMethodStatus('buttonRemoveMultiple', true);

        $this->buttonRemoveMultipleLabel = $label;
        $this->buttonRemoveMultipleTitle = $title;

        return $this;
    }

    public function errors(?AlertValidationComponentDto $validationErrors): self
    {
        $this->builder->setMethodStatus('errors', true);

        $this->validationErrors = $validationErrors;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
