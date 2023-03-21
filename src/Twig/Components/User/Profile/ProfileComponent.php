<?php

declare(strict_types=1);

namespace App\Twig\Components\User\Profile;

use App\Form\EmailChange\EMAIL_CHANGE_FORM_ERRORS;
use App\Form\PasswordChange\PASSWORD_CHANGE_FORM_ERRORS;
use App\Form\Profile\PROFILE_FORM_ERRORS;
use App\Form\Profile\PROFILE_FORM_FIELDS;
use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
use App\Twig\Components\Controls\DropZone\DropZoneComponentDto;
use App\Twig\Components\Controls\ImageAvatar\ImageAvatarComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ProfileComponent',
    template: 'Components/User/Profile/ProfileComponent.html.twig'
)]
class ProfileComponent extends TwigComponent
{
    public ProfileComponentLangDto $lang;
    public ProfileComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $nickFieldName;
    public readonly string $imageFieldName;
    public readonly string $imageRemoveFieldName;
    public readonly string $submitFieldName;
    public ImageAvatarComponentDto $imageAvatarDto;
    public DropZoneComponentDto $dropZoneDto;

    public readonly string $userRemoveFieldName;

    public static function getComponentName(): string
    {
        return 'ProfileComponent';
    }

    public function __construct(RequestStack $request, TranslatorInterface $translator)
    {
        parent::__construct($request, $translator);

        $this->formName = PROFILE_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', PROFILE_FORM_FIELDS::FORM, PROFILE_FORM_FIELDS::TOKEN);
        $this->nickFieldName = sprintf('%s[%s]', PROFILE_FORM_FIELDS::FORM, PROFILE_FORM_FIELDS::NICK);
        $this->imageFieldName = sprintf('%s[%s]', PROFILE_FORM_FIELDS::FORM, PROFILE_FORM_FIELDS::IMAGE);
        $this->imageRemoveFieldName = sprintf('%s[%s]', PROFILE_FORM_FIELDS::FORM, PROFILE_FORM_FIELDS::IMAGE_REMOVE);
        $this->submitFieldName = sprintf('%s[%s]', PROFILE_FORM_FIELDS::FORM, PROFILE_FORM_FIELDS::SUBMIT);
    }

    public function mount(ProfileComponentDto $data): void
    {
        $this->data = $data;
        $this->imageAvatarDto = $this->getImageAvatarComponentDto($data->image);
        $this->dropZoneDto = $this->getDropZoneComponentDto();

        $this->loadTranslation();
    }

    private function getImageAvatarComponentDto(string|null $imagePath): ImageAvatarComponentDto
    {
        return new ImageAvatarComponentDto(
            $imagePath,
            'http://orderlist.api/assets/img/common/user-avatar-no-image.svg',
            $this->translate('image.alt')
        );
    }

    private function getDropZoneComponentDto(): DropZoneComponentDto
    {
        return new DropZoneComponentDto(
            PROFILE_FORM_FIELDS::IMAGE,
            PROFILE_FORM_FIELDS::FORM,
            $this->translate('image.label'),
            PROFILE_FORM_FIELDS::IMAGE,
            $this->translate('image.placeholder')
        );
    }

    private function loadTranslation(): void
    {
        $this->lang = (new ProfileComponentLangDto())
            ->title(
                $this->translate('title')
            )
            ->email(
                $this->translate('email.placeholder')
            )
            ->password(
                $this->translate('password.label'),
                $this->translate('password.placeholder')
            )
            ->nick(
                $this->translate('nick.label'),
                $this->translate('nick.placeholder'),
                $this->translate('nick.msg_invalid'),
            )
            ->saveButton(
                $this->translate('button_save.label')
            )
            ->userRemove(
                $this->translate('user_remove.label'),
                $this->translate('user_remove.placeholder'),
            )
            ->validationErrors(
                $this->data->validForm ? $this->loadErrorsTranslation() : null
            )
            ->build();
    }

    private function loadErrorsTranslation(): AlertComponentDto
    {
        $errorsLang = [];
        foreach ($this->data->errors as $field => $error) {
            $errorsLang[] = match ($field) {
                EMAIL_CHANGE_FORM_ERRORS::EMAIL->value => $this->translate('validation.error.email_change.email'),
                EMAIL_CHANGE_FORM_ERRORS::PASSWORD->value => $this->translate('validation.error.email_change.password'),
                EMAIL_CHANGE_FORM_ERRORS::PASSWORD_WRONG->value, => $this->translate('validation.error.email_change.password_invalid'),

                PASSWORD_CHANGE_FORM_ERRORS::PASSWORD_OLD->value => $this->translate('validation.error.password_change.old'),
                PASSWORD_CHANGE_FORM_ERRORS::PASSWORD_NEW->value => $this->translate('validation.error.password_change.new'),
                PASSWORD_CHANGE_FORM_ERRORS::PASSWORD_CHANGE->value => $this->translate('validation.error.password_change.change'),
                PASSWORD_CHANGE_FORM_ERRORS::PASSWORD_NEW_REPEAT,
                PASSWORD_CHANGE_FORM_ERRORS::PASSWORD_REPEAT->value => $this->translate('validation.error.password_change.new_repeat'),

                PROFILE_FORM_ERRORS::NAME->value => $this->translate('validation.error.profile.name'),
                PROFILE_FORM_ERRORS::IMAGE->value => $this->translate('validation.error.profile.image'),

                default => $this->translate('validation.error.internal_server')
            };
        }

        if (!empty($errorsLang)) {
            return new AlertComponentDto(
                ALERT_TYPE::DANGER,
                '',
                '',
                array_unique($errorsLang)
            );
        }

        return new AlertComponentDto(
            ALERT_TYPE::SUCCESS,
            '',
            '',
            $this->translate('validation.ok')
        );
    }
}
