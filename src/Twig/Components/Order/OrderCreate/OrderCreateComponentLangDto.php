<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderCreate;

use App\Twig\Components\Alert\AlertComponentDto;
use Common\Domain\DtoBuilder\DtoBuilder;

class OrderCreateComponentLangDto
{
    private DtoBuilder $builder;

    public readonly string $title;
    public readonly string $productTitle;
    public readonly string $shopTitle;

    public readonly string $descriptionLabel;
    public readonly string $descriptionPlaceholder;
    public readonly string $descriptionMsgInvalid;

    public readonly string $amountLabel;
    public readonly string $amountPlaceholder;
    public readonly string $amountMsgInvalid;

    public readonly string $orderCreateButton;
    public readonly string $closeButton;

    public readonly ?AlertComponentDto $validationErrors;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
            'productAndShopTitle',
            'description',
            'amount',
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

    public function productAndShopTitle(string $productTitle, string $shopTitle): self
    {
        $this->builder->setMethodStatus('productAndShopTitle', true);

        $this->productTitle = $productTitle;
        $this->shopTitle = $shopTitle;

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

    public function amount(string $amountLabel, string $amountPlaceholder, string $amountMsgInvalid): self
    {
        $this->builder->setMethodStatus('amount', true);

        $this->amountLabel = $amountLabel;
        $this->amountPlaceholder = $amountPlaceholder;
        $this->amountMsgInvalid = $amountMsgInvalid;

        return $this;
    }

    public function buttons(string $orderCreateButton, string $closeButton): self
    {
        $this->builder->setMethodStatus('buttons', true);

        $this->orderCreateButton = $orderCreateButton;
        $this->closeButton = $closeButton;

        return $this;
    }

    public function errors(?AlertComponentDto $validationErrors): self
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
