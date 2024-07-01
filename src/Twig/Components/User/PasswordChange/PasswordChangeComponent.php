<?php

declare(strict_types=1);

namespace App\Twig\Components\User\PasswordChange;

use App\Form\User\PasswordChange\PASSWORD_CHANGE_FORM_ERRORS;
use App\Form\User\PasswordChange\PASSWORD_CHANGE_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'PasswordChangeComponent',
    template: 'Components/User/PasswordChange/PasswordChangeComponent.html.twig'
)]
class PasswordChangeComponent extends TwigComponent
{
    public PasswordChangeComponentLangDto $lang;
    public PasswordChangeComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $userIdFieldName;
    public readonly string $passwordOldFieldName;
    public readonly string $passwordNewFieldName;
    public readonly string $passwordNewRepeatFieldName;
    public readonly string $submitFieldName;
    public readonly string $tokenCsrfFieldName;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'PasswordChangeComponent';
    }

    public function mount(PasswordChangeComponentDto $data): void
    {
        $this->data = $data;

        $this->formName = PASSWORD_CHANGE_FORM_FIELDS::FORM;
        $this->userIdFieldName = sprintf('%s[%s]', PASSWORD_CHANGE_FORM_FIELDS::FORM, PASSWORD_CHANGE_FORM_FIELDS::USER_ID);
        $this->passwordOldFieldName = sprintf('%s[%s]', PASSWORD_CHANGE_FORM_FIELDS::FORM, PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_OLD);
        $this->passwordNewFieldName = sprintf('%s[%s]', PASSWORD_CHANGE_FORM_FIELDS::FORM, PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW);
        $this->passwordNewRepeatFieldName = sprintf('%s[%s]', PASSWORD_CHANGE_FORM_FIELDS::FORM, PASSWORD_CHANGE_FORM_FIELDS::PASSWORD_NEW_REPEAT);
        $this->submitFieldName = sprintf('%s[%s]', PASSWORD_CHANGE_FORM_FIELDS::FORM, PASSWORD_CHANGE_FORM_FIELDS::SUBMIT);
        $this->tokenCsrfFieldName = sprintf('%s[%s]', PASSWORD_CHANGE_FORM_FIELDS::FORM, PASSWORD_CHANGE_FORM_FIELDS::TOKEN);

        $this->loadTranslation();

        $this->titleDto = $this->createTitleDto();
    }

    private function createTitleDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title, TITLE_TYPE::POP_UP);
    }

    private function loadTranslation(): void
    {
        $this->lang = (new PasswordChangeComponentLangDto())
            ->title(
                $this->translate('title')
            )
            ->passwordOld(
                $this->translate('password_old.label'),
                $this->translate('password_old.placeholder'),
                $this->translate('password_old.msg_invalid')
            )
            ->passwordNew(
                $this->translate('password_new.label'),
                $this->translate('password_new.placeholder'),
                $this->translate('password_new.msg_invalid'),
            )
            ->passwordNewRepeat(
                $this->translate('password_new_repeat.label'),
                $this->translate('password_new_repeat.placeholder'),
                $this->translate('password_new_repeat.msg_invalid'),
            )
            ->passwordChangeButton(
                $this->translate('button_password_change.label')
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
                PASSWORD_CHANGE_FORM_ERRORS::PASSWORD_NEW->value => $this->translate('validation.error.password_new'),
                PASSWORD_CHANGE_FORM_ERRORS::PASSWORD_CHANGE->value => $this->translate('validation.error.password_change'),
                PASSWORD_CHANGE_FORM_ERRORS::PASSWORD_NEW_REPEAT,
                PASSWORD_CHANGE_FORM_ERRORS::PASSWORD_REPEAT->value => $this->translate('validation.error.password_new_repeat'),
                PASSWORD_CHANGE_FORM_ERRORS::TOKEN_EXPIRED->value => $this->translate('validation.error.token_expired'),
                PASSWORD_CHANGE_FORM_ERRORS::TRYOUT_ROUTE_PERMISSIONS->value => $this->translate('validation.error.tryout_route_permissions'),
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
