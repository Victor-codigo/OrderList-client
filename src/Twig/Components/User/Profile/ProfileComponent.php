<?php

declare(strict_types=1);

namespace App\Twig\Components\User\Profile;

use App\Form\User\Profile\PROFILE_FORM_ERRORS;
use App\Form\User\Profile\PROFILE_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\DropZone\DropZoneComponentDto;
use App\Twig\Components\Controls\ImageAvatar\ImageAvatarComponentDto;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\Config\Config;
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
    public TitleComponentDto $titleDto;

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

        $this->loadTranslation();

        $this->titleDto = $this->getTitle($this->lang->title);
        $this->imageAvatarDto = $this->getImageAvatarComponentDto($data->image);
        $this->dropZoneDto = $this->getDropZoneComponentDto();
    }

    private function getImageAvatarComponentDto(?string $imagePath): ImageAvatarComponentDto
    {
        return new ImageAvatarComponentDto(
            $imagePath,
            Config::USER_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
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

    private function getTitle(string $title): TitleComponentDto
    {
        return new TitleComponentDto($title);
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
                PROFILE_FORM_ERRORS::NAME->value => $this->translate('validation.error.profile.name'),
                PROFILE_FORM_ERRORS::IMAGE->value => $this->translate('validation.error.profile.image'),

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
        return new AlertValidationComponentDto(
            array_unique($this->data->messageOk),
            array_unique($this->data->messageErrors)
        );
    }
}
