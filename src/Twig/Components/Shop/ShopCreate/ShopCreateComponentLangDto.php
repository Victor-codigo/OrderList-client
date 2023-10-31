<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopCreate;

use App\Twig\Components\Alert\AlertComponentDto;

class ShopCreateComponentLangDto
{
    public readonly string $title;

    public readonly string $nameLabel;
    public readonly string $namePlaceholder;
    public readonly string $nameMsgInvalid;

    public readonly string $descriptionLabel;
    public readonly string $descriptionPlaceholder;
    public readonly string $descriptionMsgInvalid;

    public readonly string $imageLabel;
    public readonly string $imagePlaceholder;
    public readonly string $imageMsgInvalid;

    public readonly string $shopCreateButton;

    public readonly AlertComponentDto|null $validationErrors;

    private array $builder = [
        'title' => false,
        'name' => false,
        'description' => false,
        'image' => false,
        'submit' => false,
        'errors' => false,
        'build' => false,
    ];

    public function title(string $title): self
    {
        $this->builder['title'] = true;

        $this->title = $title;

        return $this;
    }

    public function name(string $nameLabel, string $namePlaceholder, string $nameMsgInvalid): self
    {
        $this->builder['name'] = true;

        $this->nameLabel = $nameLabel;
        $this->namePlaceholder = $namePlaceholder;
        $this->nameMsgInvalid = $nameMsgInvalid;

        return $this;
    }

    public function description(string $descriptionLabel, string $descriptionPlaceholder, string $descriptionMsgInvalid): self
    {
        $this->builder['description'] = true;

        $this->descriptionLabel = $descriptionLabel;
        $this->descriptionPlaceholder = $descriptionPlaceholder;
        $this->descriptionMsgInvalid = $descriptionMsgInvalid;

        return $this;
    }

    public function image(string $imageLabel, string $imagePlaceholder, string $imageMsgInvalid): self
    {
        $this->builder['image'] = true;

        $this->imageLabel = $imageLabel;
        $this->imagePlaceholder = $imagePlaceholder;
        $this->imageMsgInvalid = $imageMsgInvalid;

        return $this;
    }

    public function submitButton(string $shopCreateButton): self
    {
        $this->builder['submit'] = true;

        $this->shopCreateButton = $shopCreateButton;

        return $this;
    }

    public function errors(AlertComponentDto|null $validationErrors): self
    {
        $this->builder['errors'] = true;

        $this->validationErrors = $validationErrors;

        return $this;
    }

    public function build(): self
    {
        $this->builder['build'] = true;

        if (count(array_filter($this->builder)) < count($this->builder)) {
            throw new \InvalidArgumentException('Constructors: title, name, description, image, errors. Are mandatory');
        }

        return $this;
    }
}
