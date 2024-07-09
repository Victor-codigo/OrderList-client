<?php

declare(strict_types=1);

namespace App\Twig\Components\Legal\Privacy;

use Common\Domain\DtoBuilder\DtoBuilder;

class PrivacyComponentLangDto
{
    private DtoBuilder $builder;

    public readonly string $title;

    public readonly string $nameLabel;
    public readonly string $namePlaceholder;
    public readonly string $nameMsgInvalid;

    public readonly string $descriptionLabel;
    public readonly string $descriptionPlaceholder;
    public readonly string $descriptionMsgInvalid;

    public readonly string $groupCreateButtonLabel;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
            'name',
            'description',
            'createButton',
        ]);
    }

    public function title(string $title): self
    {
        $this->builder->setMethodStatus('title', true);

        $this->title = $title;

        return $this;
    }

    public function name(string $label, string $placeholder, string $msgInvalid): self
    {
        $this->builder->setMethodStatus('name', true);

        $this->nameLabel = $label;
        $this->namePlaceholder = $placeholder;
        $this->nameMsgInvalid = $msgInvalid;

        return $this;
    }

    public function description(string $label, string $placeholder, string $msgInvalid): self
    {
        $this->builder->setMethodStatus('description', true);

        $this->descriptionLabel = $label;
        $this->descriptionPlaceholder = $placeholder;
        $this->descriptionMsgInvalid = $msgInvalid;

        return $this;
    }

    public function createButton(string $label): self
    {
        $this->builder->setMethodStatus('createButton', true);

        $this->groupCreateButtonLabel = $label;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
