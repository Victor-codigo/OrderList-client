<?php

declare(strict_types=1);

namespace App\Twig\Components\User\EmailChange;

use App\Twig\Components\Alert\AlertComponentDto;
use InvalidArgumentException;

class EmailChangeComponentLangDto
{
    public readonly string $title;

    public readonly string $emailLabel;
    public readonly string $emailPlaceholder;
    public readonly string $emailMsgInvalid;

    public readonly string $passwordLabel;
    public readonly string $passwordPlaceholder;
    public readonly string $passwordMsgInvalid;

    public readonly string $emailChangeButton;

    public readonly AlertComponentDto $validationErrors;

    private array $builder = [
        'title' => false,
        'email' => false,
        'password' => false,
        'emailChangeButton' => false,
        'validationErrors' => false,
        'build' => false
    ];

    public function title(string $title): static
    {
        $this->builder['title'] = true;

        $this->title = $title;

        return $this;
    }

    public function email(string $emailLabel, string $emailPlaceholder, string $emailMsgInvalid): static
    {
        $this->builder['email'] = true;

        $this->emailLabel = $emailLabel;
        $this->emailPlaceholder = $emailPlaceholder;
        $this->emailMsgInvalid = $emailMsgInvalid;

        return $this;
    }

    public function password(string $passwordLabel, string $passwordPlaceholder, string $passwordMsgInvalid): static
    {
        $this->builder['password'] = true;

        $this->passwordLabel = $passwordLabel;
        $this->passwordPlaceholder = $passwordPlaceholder;
        $this->passwordMsgInvalid = $passwordMsgInvalid;

        return $this;
    }

    public function emailChangeButton(string $emailChangeButton): static
    {
        $this->builder['emailChangeButton'] = true;

        $this->emailChangeButton = $emailChangeButton;

        return $this;
    }

    public function validationErrors(AlertComponentDto $validationErrors): static
    {
        $this->builder['validationErrors'] = true;

        $this->validationErrors = $validationErrors;

        return $this;
    }

    public function build(): static
    {
        $this->builder['build'] = true;

        if (count(array_filter($this->builder))<count($this->builder)) {
            throw new InvalidArgumentException(
                'Constructors: title, email, password, emailChangeButton, validationErrors. Are mandatory'
            );
        }

        return $this;
    }
}
