<?php

namespace App\Twig\Components\User\Login;

use App\Form\User\Login\LOGIN_FORM_ERRORS;
use App\Form\User\Login\LOGIN_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\Title\TitleComponentDto;
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
    public readonly string $tokenCsrfFieldName;
    public readonly string $captchaFieldName;
    public readonly string $emailFieldName;
    public readonly string $passwordFieldName;
    public readonly string $rememberMeFieldName;
    public readonly string $submitFieldName;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'LoginComponent';
    }

    public function mount(LoginComponentDto $data): void
    {
        $this->data = $data;

        $this->formName = LOGIN_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::TOKEN);
        $this->captchaFieldName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::CAPTCHA);
        $this->emailFieldName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::EMAIL);
        $this->passwordFieldName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::PASSWORD);
        $this->rememberMeFieldName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::REMEMBER_ME);
        $this->submitFieldName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::SUBMIT);

        $this->loadTranslation();

        $this->titleDto = $this->createTitleDto();
    }

    private function createTitleDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title);
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
            $this->data->validForm ? $this->createAlertValidationComponentDto() : null
        );
    }

    /**
     * @return string[]
     */
    public function loadErrorsTranslation(array $errors): array
    {
        $errorsLang = [];
        foreach ($errors as $field => $error) {
            $errorsLang[] = match ($field) {
                LOGIN_FORM_ERRORS::CAPTCHA->value => $this->translate('validation.error.captcha'),
                LOGIN_FORM_ERRORS::LOGIN->value,
                LOGIN_FORM_ERRORS::EMAIL->value,
                LOGIN_FORM_ERRORS::PASSWORD->value => $this->translate('validation.error.login'),
                default => $this->translate('validation.error.internal_server')
            };
        }

        return $errorsLang;
    }

    private function createAlertValidationComponentDto(): AlertValidationComponentDto
    {
        return new AlertValidationComponentDto(
            [],
            array_unique($this->data->messagesErrors)
        );
    }
}
