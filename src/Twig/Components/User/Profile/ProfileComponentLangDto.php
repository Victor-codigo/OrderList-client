<?php

declare(strict_types=1);

namespace App\Twig\Components\User\Profile;

use App\Twig\Components\Alert\AlertComponentDto;

class ProfileComponentLangDto
{
    public string $title;
    // --
    public string $emailPlaceholder;
    // --
    public string $passwordLabel;
    public string $passwordPlaceholder;
    // --
    public string $nickLabel;
    public string $nickPlaceholder;
    public string $nickMsgInvalid;
    // --
    public AlertComponentDto|null $validationErrors;
    public string $saveButton;

    public string $userRemoveLabel;
    public string $userRemovePlaceholder;

    private array $builder = [
        'title' => false,
        'email' => false,
        'password' => false,
        'nick' => false,
        'saveButton' => false,
        'validationErrors' => false,
        'userRemove' => false,
        'build' => false,
    ];

    public function __construct()
    {
    }

    public function title(string $title): static
    {
        $this->builder['title'] = true;

        $this->title = $title;

        return $this;
    }

    public function email(string $emailPlaceholder): static
    {
        $this->builder['email'] = true;

        $this->emailPlaceholder = $emailPlaceholder;

        return $this;
    }

    public function password(string $passwordLabel, string $passwordPlaceholder): static
    {
        $this->builder['password'] = true;

        $this->passwordLabel = $passwordLabel;
        $this->passwordPlaceholder = $passwordPlaceholder;

        return $this;
    }

    public function nick(string $nickLabel, string $nickPlaceholder, string $nickMsgInvalid): static
    {
        $this->builder['nick'] = true;

        $this->nickLabel = $nickLabel;
        $this->nickPlaceholder = $nickPlaceholder;
        $this->nickMsgInvalid = $nickMsgInvalid;

        return $this;
    }

    public function userRemove(string $userRemoveLabel, string $userRemovePlaceholder): static
    {
        $this->builder['userRemove'] = true;

        $this->userRemoveLabel = $userRemoveLabel;
        $this->userRemovePlaceholder = $userRemovePlaceholder;

        return $this;
    }

    public function saveButton(string $saveButton): static
    {
        $this->builder['saveButton'] = true;

        $this->saveButton = $saveButton;

        return $this;
    }

    public function validationErrors(AlertComponentDto|null $validationErrors): static
    {
        $this->builder['validationErrors'] = true;

        $this->validationErrors = $validationErrors;

        return $this;
    }

    public function build(): static
    {
        $this->builder['build'] = true;

        if (count(array_filter($this->builder)) < count($this->builder)) {
            throw new \InvalidArgumentException(sprintf('Constructors [%s]. Are mandatory', implode(', ', $this->builder)));
        }

        return $this;
    }
}
