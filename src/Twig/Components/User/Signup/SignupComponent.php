<?php

declare(strict_types=1);

namespace App\Twig\Components\User\Signup;

use App\Form\Signup\SIGNUP_FORM_ERRORS;
use App\Form\Signup\SIGNUP_FORM_FIELDS;
use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'SignupComponent',
    template: 'Components/User/Signup/SignupComponent.html.twig'
)]
class SignupComponent extends TwigComponent
{
    public SignupComponentLangDto $lang;
    public SignupComponentDto|TwigComponentDtoInterface $data;

    public readonly string $tokenCsrfFiledName;
    public readonly string $emailFiledName;
    public readonly string $passwordFiledName;
    public readonly string $passwordRepeatedFiledName;
    public readonly string $nickFiledName;

    protected static function getComponentName(): string
    {
        return 'SignupComponent';
    }

    public function mount(SignupComponentDto $data): void
    {
        $this->data = $data;

        $this->formName = SIGNUP_FORM_FIELDS::FORM;
        $this->tokenCsrfFiledName = sprintf("%s[%s]", SIGNUP_FORM_FIELDS::FORM, SIGNUP_FORM_FIELDS::TOKEN);
        $this->emailFiledName = sprintf("%s[%s]", SIGNUP_FORM_FIELDS::FORM, SIGNUP_FORM_FIELDS::EMAIL);
        $this->passwordFiledName = sprintf("%s[%s]", SIGNUP_FORM_FIELDS::FORM, SIGNUP_FORM_FIELDS::PASSWORD);
        $this->passwordRepeatedFiledName = sprintf("%s[%s]", SIGNUP_FORM_FIELDS::FORM, SIGNUP_FORM_FIELDS::PASSWORD_REPEATED);
        $this->nickFiledName = sprintf("%s[%s]", SIGNUP_FORM_FIELDS::FORM, SIGNUP_FORM_FIELDS::NICK);
        $this->submitFiledName = sprintf("%s[%s]", SIGNUP_FORM_FIELDS::FORM, SIGNUP_FORM_FIELDS::SUBMIT);

        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->lang = new SignupComponentLangDto(
            $this->translate('title'),
            $this->translate('email.label'),
            $this->translate('email.placeholder'),
            $this->translate('email.msg_invalid'),
            $this->translate('password.label'),
            $this->translate('password.placeholder'),
            $this->translate('password.msg_invalid'),
            $this->translate('password_repeated.label'),
            $this->translate('password_repeated.placeholder'),
            $this->translate('password_repeated.msg_invalid'),
            $this->translate('nick.label'),
            $this->translate('nick.placeholder'),
            $this->translate('nick.msg_invalid'),
            $this->translate('register_button'),
            $this->translate('login_link'),
            $this->loadErrorsTranslation()
        );
    }

    private function loadErrorsTranslation(): AlertComponentDto
    {
        $errorsLang = [];
        foreach ($this->data->errors as $error => $errorValue) {
            $errorsLang[] = match ($error) {
                SIGNUP_FORM_ERRORS::EMAIL->value => $this->translate('email.msg_invalid'),
                SIGNUP_FORM_ERRORS::PASSWORD->value => $this->translate('password.msg_invalid'),
                SIGNUP_FORM_ERRORS::NAME->value => $this->translate('nick.msg_invalid'),
                SIGNUP_FORM_ERRORS::EMAIL_EXISTS->value => $this->translate('validation.error.email_exists'),
                default => $this->translate('validation.error.internal_server')
            };
        }

        return new AlertComponentDto(
            ALERT_TYPE::DANGER,
            $this->translate('validation.title'),
            '',
            $errorsLang
        );
    }
}
