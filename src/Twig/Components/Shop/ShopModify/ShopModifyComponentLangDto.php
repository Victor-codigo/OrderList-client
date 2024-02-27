<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopModify;

use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use Common\Domain\DtoBuilder\DtoBuilder;

class ShopModifyComponentLangDto
{
    private readonly DtoBuilder $builder;

    public readonly string $title;
    public readonly string $productsTitle;

    public readonly string $nameLabel;
    public readonly string $namePlaceholder;
    public readonly string $nameMsgInvalid;

    public readonly string $descriptionLabel;
    public readonly string $descriptionPlaceholder;
    public readonly string $descriptionMsgInvalid;

    public readonly string $imageLabel;
    public readonly string $imagePlaceholder;
    public readonly string $imageMsgInvalid;

    public readonly string $shopModifyButton;
    public readonly string $closeButton;

    public readonly AlertValidationComponentDto|null $validationErrors;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
            'name',
            'productsTitle',
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

    public function name(string $nameLabel, string $namePlaceholder, string $nameMsgInvalid): self
    {
        $this->builder->setMethodStatus('name', true);

        $this->nameLabel = $nameLabel;
        $this->namePlaceholder = $namePlaceholder;
        $this->nameMsgInvalid = $nameMsgInvalid;

        return $this;
    }

    public function productsTitle(string $title): self
    {
        $this->builder->setMethodStatus('productsTitle', true);

        $this->productsTitle = $title;

        return $this;
    }

    public function description(string $descriptionLabel, string $descriptionPlaceholder, string $descriptionMsgInvalid): self
    {
        $this->builder->setMethodStatus('description', true);

        $this->descriptionLabel = $descriptionLabel;
        $this->descriptionPlaceholder = $descriptionPlaceholder;
        $this->descriptionMsgInvalid = $descriptionMsgInvalid;

        return $this;
    }

    public function image(string $imageLabel, string $imagePlaceholder, string $imageMsgInvalid): self
    {
        $this->builder->setMethodStatus('image', true);

        $this->imageLabel = $imageLabel;
        $this->imagePlaceholder = $imagePlaceholder;
        $this->imageMsgInvalid = $imageMsgInvalid;

        return $this;
    }

    public function buttons(string $shopModifyButton, string $closeButton): self
    {
        $this->builder->setMethodStatus('buttons', true);

        $this->shopModifyButton = $shopModifyButton;
        $this->closeButton = $closeButton;

        return $this;
    }

    public function errors(AlertValidationComponentDto|null $validationErrors): self
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
