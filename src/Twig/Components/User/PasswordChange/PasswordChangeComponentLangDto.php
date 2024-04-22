<?php

declare(strict_types=1);

namespace App\Twig\Components\User\PasswordChange;

use App\Twig\Components\AlertValidation\AlertValidationComponentDto;

class PasswordChangeComponentLangDto
{
    public readonly string $title;

    public readonly string $passwordOldLabel;
    public readonly string $passwordOldPlaceholder;
    public readonly string $passwordOldMsgInvalid;

    public readonly string $passwordNewLabel;
    public readonly string $passwordNewPlaceholder;
    public readonly string $passwordNewMsgInvalid;

    public readonly string $passwordNewRepeatLabel;
    public readonly string $passwordNewRepeatPlaceholder;
    public readonly string $passwordNewRepeatMsgInvalid;

    public readonly string $passwordChangeButton;

    public readonly ?AlertValidationComponentDto $validationErrors;

    private array $builder = [
        'title' => false,
        'passwordOld' => false,
        'passwordNew' => false,
        'passwordNewRepeat' => false,
        'passwordChangeButton' => false,
        'validationErrors' => false,
        'build' => false,
    ];

    public function title(string $title): static
    {
        $this->builder['title'] = true;

        $this->title = $title;

        return $this;
    }

    public function passwordOld(string $passwordOldLabel, string $passwordOldPlaceholder, string $passwordOldMsgInvalid): static
    {
        $this->builder['passwordOld'] = true;

        $this->passwordOldLabel = $passwordOldLabel;
        $this->passwordOldPlaceholder = $passwordOldPlaceholder;
        $this->passwordOldMsgInvalid = $passwordOldMsgInvalid;

        return $this;
    }

    public function passwordNew(string $passwordNewLabel, string $passwordNewPlaceholder, string $passwordNewMsgInvalid): static
    {
        $this->builder['passwordNew'] = true;

        $this->passwordNewLabel = $passwordNewLabel;
        $this->passwordNewPlaceholder = $passwordNewPlaceholder;
        $this->passwordNewMsgInvalid = $passwordNewMsgInvalid;

        return $this;
    }

    public function passwordNewRepeat(string $passwordNewRepeatLabel, string $passwordNewRepeatPlaceholder, string $passwordNewRepeatMsgInvalid): static
    {
        $this->builder['passwordNewRepeat'] = true;

        $this->passwordNewRepeatLabel = $passwordNewRepeatLabel;
        $this->passwordNewRepeatPlaceholder = $passwordNewRepeatPlaceholder;
        $this->passwordNewRepeatMsgInvalid = $passwordNewRepeatMsgInvalid;

        return $this;
    }

    public function passwordChangeButton(string $passwordChangeButton): static
    {
        $this->builder['passwordChangeButton'] = true;

        $this->passwordChangeButton = $passwordChangeButton;

        return $this;
    }

    public function validationErrors(?AlertValidationComponentDto $validationErrors): static
    {
        $this->builder['validationErrors'] = true;

        $this->validationErrors = $validationErrors;

        return $this;
    }

    public function build(): static
    {
        $this->builder['build'] = true;

        if (count(array_filter($this->builder)) < count($this->builder)) {
            throw new \InvalidArgumentException('Constructors: title, passwordOld, passwordNew, passwordNewRepeat, passwordChangeButton, validationErrors. Are mandatory');
        }

        return $this;
    }
}
