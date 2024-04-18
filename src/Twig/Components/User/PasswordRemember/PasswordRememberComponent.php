<?php

declare(strict_types=1);

namespace App\Twig\Components\User\PasswordRemember;

use App\Form\User\PasswordRemember\PASSWORD_REMEMBER_FORM_ERRORS;
use App\Form\User\PasswordRemember\PASSWORD_REMEMBER_FORM_FIELDS;
use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'PasswordRememberComponent',
    template: 'Components/User/PasswordRemember/PasswordRemember.html.twig'
)]
class PasswordRememberComponent extends TwigComponent
{
    public PasswordRememberLangDto $lang;
    public PasswordRememberDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $emailFieldName;
    public readonly string $submitFieldName;

    public static function getComponentName(): string
    {
        return 'PassowrdRememberComponent';
    }

    public function mount(PasswordRememberDto $data): void
    {
        $this->data = $data;

        $this->formName = PASSWORD_REMEMBER_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', PASSWORD_REMEMBER_FORM_FIELDS::FORM, PASSWORD_REMEMBER_FORM_FIELDS::TOKEN);
        $this->emailFieldName = sprintf('%s[%s]', PASSWORD_REMEMBER_FORM_FIELDS::FORM, PASSWORD_REMEMBER_FORM_FIELDS::EMAIL);
        $this->submitFieldName = sprintf('%s[%s]', PASSWORD_REMEMBER_FORM_FIELDS::FORM, PASSWORD_REMEMBER_FORM_FIELDS::SUBMIT);

        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->lang = new PasswordRememberLangDto(
            $this->translate('title'),
            $this->translate('email.label'),
            $this->translate('email.placeholder'),
            $this->translate('email.msg_invalid'),
            $this->translate('submit.name'),
            $this->loadErrorsTranslation()
        );
    }

    private function loadErrorsTranslation(): AlertComponentDto
    {
        $errorsLang = [];
        foreach ($this->data->errors as $field => $error) {
            $errorsLang[] = match ($field) {
                PASSWORD_REMEMBER_FORM_ERRORS::EMAIL->value => $this->translate('validation.error.email'),
                PASSWORD_REMEMBER_FORM_ERRORS::EMAIL_NOT_FOUND->value => $this->translate('validation.error.remember'),
                default => $this->translate('validation.error.internal_server')
            };
        }

        return new AlertComponentDto(
            ALERT_TYPE::DANGER,
            $this->translate('validation.title'),
            '',
            array_unique($errorsLang)
        );
    }
}
