<?php

declare(strict_types=1);

namespace App\Twig\Components\User\UserRemove;

use App\Form\User\UserRemove\USER_REMOVE_FORM_FIELDS;
use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\Title\TITLE_TYPE;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'UserRemoveComponent',
    template: 'Components/User/UserRemove/UserRemoveComponent.html.twig'
)]
class UserRemoveComponent extends TwigComponent
{
    public UserRemoveComponentLangDto $lang;
    public UserRemoveComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $submitFieldName;
    public readonly string $userIdFieldName;
    public readonly string $tokenCsrfFieldName;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'UserRemoveComponent';
    }

    public function __construct(RequestStack $request, TranslatorInterface $translator)
    {
        parent::__construct($request, $translator);

        $this->formName = USER_REMOVE_FORM_FIELDS::FORM;
        $this->submitFieldName = sprintf('%s[%s]', USER_REMOVE_FORM_FIELDS::FORM, USER_REMOVE_FORM_FIELDS::SUBMIT);
        $this->userIdFieldName = sprintf('%s[%s]', USER_REMOVE_FORM_FIELDS::FORM, USER_REMOVE_FORM_FIELDS::USER_ID);
        $this->tokenCsrfFieldName = sprintf('%s[%s]', USER_REMOVE_FORM_FIELDS::FORM, USER_REMOVE_FORM_FIELDS::TOKEN);
    }

    public function mount(UserRemoveComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();

        $this->titleDto = $this->createTitleDto();
    }

    private function createTitleDto(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title, TITLE_TYPE::POP_UP);
    }

    private function loadTranslation(): void
    {
        $this->lang = (new UserRemoveComponentLangDto())
            ->title(
                $this->translate('title')
            )
            ->messageAdvice(
                $this->translate('message_advice.text')
            )
            ->userRemoveButton(
                $this->translate('remove_user_button.label')
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
