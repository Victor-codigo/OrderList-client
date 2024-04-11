<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersCreateFrom;

use App\Twig\Components\Alert\AlertComponentDto;
use Common\Domain\DtoBuilder\DtoBuilder;

class ListOrdersCreateFromComponentLangDto
{
    private DtoBuilder $builder;

    public readonly string $title;

    public readonly string $nameLabel;
    public readonly string $namePlaceholder;
    public readonly string $nameMsgInvalid;

    public readonly string $listOrdersButtonLabel;
    public readonly string $listOrdersButtonTitle;

    public readonly string $listOrdersLabel;
    public readonly string $listOrdersTitle;
    public readonly string $listOrdersMsgInvalid;
    public readonly string $listOrdersPlaceholder;

    public readonly string $listOrdersCreateFromButton;

    public readonly ?AlertComponentDto $validationErrors;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
            'name',
            'listOrders',
            'submitButton',
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

    public function listOrders(string $listOrdersLabel, string $listoOrdersPlaceholder, string $listOrdersMsgInvalid, string $buttonLabel, string $buttonTitle): self
    {
        $this->builder->setMethodStatus('listOrders', true);

        $this->listOrdersLabel = $listOrdersLabel;
        $this->listOrdersPlaceholder = $listoOrdersPlaceholder;
        $this->listOrdersButtonLabel = $buttonLabel;
        $this->listOrdersButtonTitle = $buttonTitle;
        $this->listOrdersMsgInvalid = $listOrdersMsgInvalid;

        return $this;
    }

    public function submitButton(string $listOrdersCreateFromLabel): self
    {
        $this->builder->setMethodStatus('submitButton', true);

        $this->listOrdersCreateFromButton = $listOrdersCreateFromLabel;

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
