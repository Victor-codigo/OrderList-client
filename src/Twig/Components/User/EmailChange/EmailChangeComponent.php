<?php

declare(strict_types=1);

namespace App\Twig\Components\User\EmailChange;

use App\Form\EmailChange\EMAIL_CHANGE_FORM_ERRORS;
use App\Form\EmailChange\EMAIL_CHANGE_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'EmailChangeComponent',
    template: 'Components/User/EmailChange/EmailChangeComponent.html.twig'
)]
class EmailChangeComponent extends TwigComponent
{
    public EmailChangeComponentLangDto $lang;
    public EmailChangeComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $emailFieldName;
    public readonly string $passwordFieldName;
    public readonly string $submitFieldName;
    public readonly string $tokenCsrfFieldName;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'EmailChangeComponent';
    }

    public function __construct(RequestStack $request, TranslatorInterface $translator)
    {
        parent::__construct($request, $translator);

        $this->formName = EMAIL_CHANGE_FORM_FIELDS::FORM;
        $this->emailFieldName = sprintf('%s[%s]', EMAIL_CHANGE_FORM_FIELDS::FORM, EMAIL_CHANGE_FORM_FIELDS::EMAIL);
        $this->passwordFieldName = sprintf('%s[%s]', EMAIL_CHANGE_FORM_FIELDS::FORM, EMAIL_CHANGE_FORM_FIELDS::PASSWORD);
        $this->submitFieldName = sprintf('%s[%s]', EMAIL_CHANGE_FORM_FIELDS::FORM, EMAIL_CHANGE_FORM_FIELDS::SUBMIT);
        $this->tokenCsrfFieldName = sprintf('%s[%s]', EMAIL_CHANGE_FORM_FIELDS::FORM, EMAIL_CHANGE_FORM_FIELDS::TOKEN);
    }

    public function mount(EmailChangeComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();

        $this->titleDto = $this->createTitleDto();
    }

    private function createTitleDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title);
    }

    private function loadTranslation(): void
    {
        $this->lang = (new EmailChangeComponentLangDto())
            ->title(
                $this->translate('title')
            )
            ->email(
                $this->translate('email.label'),
                $this->translate('email.placeholder'),
                $this->translate('email.msg_invalid')
            )
            ->password(
                $this->translate('password.label'),
                $this->translate('password.placeholder'),
                $this->translate('password.msg_invalid')
            )
            ->emailChangeButton(
                $this->translate('button_email_change.label')
            )
            ->validationErrors(
                $this->data->validForm ? $this->createAlertValidationComponentDto() : null
            )
        ->build();
    }

    /**
     * @return string[]
     */
    public function loadErrorsTranslation(array $errors): array
    {
        $errorsLang = [];
        foreach ($errors as $field => $error) {
            $errorsLang[] = match ($field) {
                EMAIL_CHANGE_FORM_ERRORS::EMAIL->value => $this->translate('validation.error.email'),
                EMAIL_CHANGE_FORM_ERRORS::PASSWORD->value => $this->translate('validation.error.password'),
                EMAIL_CHANGE_FORM_ERRORS::PASSWORD_WRONG->value, => $this->translate('validation.error.password_invalid'),
                default => $this->translate('validation.error.internal_server')
            };
        }

        return $errorsLang;
    }

    public function loadValidationOkTranslation(): string
    {
        return $this->translate('validation.ok');
    }

    private function createAlertValidationComponentDto(): AlertValidationComponentDto
    {
        $errorsLang = $this->loadErrorsTranslation($this->data->errors);

        return new AlertValidationComponentDto(
            array_unique([$this->loadValidationOkTranslation()]),
            array_unique($errorsLang)
        );
    }
}
