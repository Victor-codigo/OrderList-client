<?php

namespace App\Twig\Components\User\Login;

use App\Form\Login\LOGIN_FORM_ERRORS;
use App\Form\Login\LOGIN_FORM_FIELDS;
use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'LoginComponent',
    template: 'Components/User/Login/LoginComponent.html.twig'
)]
final class LoginComponent extends TwigComponent
{
    public LoginComponentLangDto $lang;
    public LoginComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFiledName;
    public readonly string $emailFiledName;
    public readonly string $passwordFiledName;
    public readonly string $rememberMeFiledName;
    public readonly string $submitFiledName;

    public static function getComponentName(): string
    {
        return 'LoginComponent';
    }

    public function mount(LoginComponentDto $data): void
    {
        $this->data = $data;

        $this->formName = LOGIN_FORM_FIELDS::FORM;
        $this->tokenCsrfFiledName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::TOKEN);
        $this->emailFiledName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::EMAIL);
        $this->passwordFiledName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::PASSWORD);
        $this->rememberMeFiledName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::REMEMBER_ME);
        $this->submitFiledName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::SUBMIT);

        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->lang = new LoginComponentLangDto(
            $this->translate('title'),
            $this->translate('email.label'),
            $this->translate('email.placeholder'),
            $this->translate('email.msg_invalid'),
            $this->translate('password.label'),
            $this->translate('password.placeholder'),
            $this->translate('password.msg_invalid'),
            $this->translate('login_button'),
            $this->translate('remember_login'),
            $this->translate('password_forget'),
            $this->translate('register'),
            $this->loadErrorsTranslation()
        );
    }

    private function loadErrorsTranslation(): AlertComponentDto
    {
        $errorsLang = [];
        foreach ($this->data->errors as $field => $error) {
            $errorsLang[] = match ($field) {
                LOGIN_FORM_ERRORS::LOGIN->value,
                LOGIN_FORM_ERRORS::EMAIL->value,
                LOGIN_FORM_ERRORS::PASSWORD->value => $this->translate('validation.error.login'),
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
