<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopCreate;

use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use Common\Domain\DtoBuilder\DtoBuilder;

class ShopCreateComponentLangDto
{
    protected DtoBuilder $builder;

    public readonly string $title;
    public readonly string $productsTitle;

    public readonly string $nameLabel;
    public readonly string $namePlaceholder;
    public readonly string $nameMsgInvalid;

    public readonly string $addressLabel;
    public readonly string $addressPlaceholder;
    public readonly string $addressMsgInvalid;

    public readonly string $descriptionLabel;
    public readonly string $descriptionPlaceholder;
    public readonly string $descriptionMsgInvalid;

    public readonly string $imageLabel;
    public readonly string $imagePlaceholder;
    public readonly string $imageMsgInvalid;

    public readonly string $shopCreateButtonLabel;
    public readonly string $closeButton;

    public readonly ?AlertValidationComponentDto $validationErrors;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
            'name',
            'address',
            'shopsTitle',
            'description',
            'image',
            'buttons',
            'errors',
        ]);
    }

    public function title(string $title): self
    {
        $this->builder->setMethodStatus('title', true);

        $this->title = $title;

        return $this;
    }

    public function name(string $nameLabel, string $namePlaceholder, string $nameMsgInvalid): static
    {
        $this->builder->setMethodStatus('name', true);

        $this->nameLabel = $nameLabel;
        $this->namePlaceholder = $namePlaceholder;
        $this->nameMsgInvalid = $nameMsgInvalid;

        return $this;
    }

    public function address(string $addressLabel, string $addressPlaceholder, string $addressMsgInvalid): static
    {
        $this->builder->setMethodStatus('address', true);

        $this->addressLabel = $addressLabel;
        $this->addressPlaceholder = $addressPlaceholder;
        $this->addressMsgInvalid = $addressMsgInvalid;

        return $this;
    }

    public function productsTitle(string $title): self
    {
        $this->builder->setMethodStatus('shopsTitle', true);

        $this->productsTitle = $title;

        return $this;
    }

    public function description(string $descriptionLabel, string $descriptionPlaceholder, string $descriptionMsgInvalid): static
    {
        $this->builder->setMethodStatus('description', true);

        $this->descriptionLabel = $descriptionLabel;
        $this->descriptionPlaceholder = $descriptionPlaceholder;
        $this->descriptionMsgInvalid = $descriptionMsgInvalid;

        return $this;
    }

    public function image(string $imageLabel, string $imagePlaceholder, string $imageMsgInvalid): static
    {
        $this->builder->setMethodStatus('image', true);

        $this->imageLabel = $imageLabel;
        $this->imagePlaceholder = $imagePlaceholder;
        $this->imageMsgInvalid = $imageMsgInvalid;

        return $this;
    }

    public function buttons(string $shopCreateButton, string $closeButton): self
    {
        $this->builder->setMethodStatus('buttons', true);

        $this->shopCreateButtonLabel = $shopCreateButton;
        $this->closeButton = $closeButton;

        return $this;
    }

    public function errors(?AlertValidationComponentDto $validationErrors): static
    {
        $this->builder->setMethodStatus('errors', true);

        $this->validationErrors = $validationErrors;

        return $this;
    }

    public function build(): static
    {
        $this->builder->validate();

        return $this;
    }
}
