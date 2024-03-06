<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersModify;

use App\Twig\Components\Alert\AlertComponentDto;
use Common\Domain\DtoBuilder\DtoBuilder;

class ListOrdersModifyComponentLangDto
{
    private DtoBuilder $builder;

    public readonly string $title;

    public readonly string $nameLabel;
    public readonly string $namePlaceholder;
    public readonly string $nameMsgInvalid;

    public readonly string $descriptionLabel;
    public readonly string $descriptionPlaceholder;
    public readonly string $descriptionMsgInvalid;

    public readonly string $dateToBuyLabel;
    public readonly string $dateToBuyPlaceholder;
    public readonly string $dateToBuyMsgInvalid;

    public readonly string $listOrdersModifyButton;

    public readonly ?AlertComponentDto $validationErrors;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
            'name',
            'description',
            'dateToBuy',
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

    public function description(string $descriptionLabel, string $descriptionPlaceholder, string $descriptionMsgInvalid): self
    {
        $this->builder->setMethodStatus('description', true);

        $this->descriptionLabel = $descriptionLabel;
        $this->descriptionPlaceholder = $descriptionPlaceholder;
        $this->descriptionMsgInvalid = $descriptionMsgInvalid;

        return $this;
    }

    public function dateToBuy(string $dateToBuyLabel, string $dateToBuyPlaceholder, string $dateToBuyMsgInvalid): self
    {
        $this->builder->setMethodStatus('dateToBuy', true);

        $this->dateToBuyLabel = $dateToBuyLabel;
        $this->dateToBuyPlaceholder = $dateToBuyPlaceholder;
        $this->dateToBuyMsgInvalid = $dateToBuyMsgInvalid;

        return $this;
    }

    public function submitButton(string $listOrdersModifyLabel): self
    {
        $this->builder->setMethodStatus('submitButton', true);

        $this->listOrdersModifyButton = $listOrdersModifyLabel;

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
