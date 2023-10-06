<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersCreate;

use App\Twig\Components\Alert\AlertComponentDto;

class ListOrdersCreateComponentLangDto
{
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

    public readonly string $userGroupsLabel;
    public readonly string $userGroupsPlaceholder;
    public readonly string $userGroupsMsgInvalid;

    public readonly string $listOrdersCreateButton;

    public readonly AlertComponentDto|null $validationErrors;

    private array $builder = [
        'title' => false,
        'name' => false,
        'description' => false,
        'dateToBuy' => false,
        'userGroups' => false,
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

    public function dateToBuy(string $dateToBuyLabel, string $dateToBuyPlaceholder, string $dateToBuyMsgInvalid): self
    {
        $this->builder['dateToBuy'] = true;

        $this->dateToBuyLabel = $dateToBuyLabel;
        $this->dateToBuyPlaceholder = $dateToBuyPlaceholder;
        $this->dateToBuyMsgInvalid = $dateToBuyMsgInvalid;

        return $this;
    }

    public function userGroups(string $userGroupsLabel, string $userGroupsPlaceholder, string $userGroupsMsgInvalid): self
    {
        $this->builder['userGroups'] = true;

        $this->userGroupsLabel = $userGroupsLabel;
        $this->userGroupsPlaceholder = $userGroupsPlaceholder;
        $this->userGroupsMsgInvalid = $userGroupsMsgInvalid;

        return $this;
    }

    public function submitButton(string $listOrdersCreateLabel): self
    {
        $this->builder['submit'] = true;

        $this->listOrdersCreateButton = $listOrdersCreateLabel;

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
            throw new \InvalidArgumentException('Constructors: title, name, description, dateToBuy, userGroups, errors. Are mandatory');
        }

        return $this;
    }
}
