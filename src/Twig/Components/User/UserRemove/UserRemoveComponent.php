<?php

declare(strict_types=1);

namespace App\Twig\Components\User\UserRemove;

use App\Form\UserRemove\USER_REMOVE_FORM_FIELDS;
use App\Twig\Components\Alert\ALERT_TYPE;
use App\Twig\Components\Alert\AlertComponentDto;
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
    public readonly string $tokenCsrfFieldName;

    public static function getComponentName(): string
    {
        return 'UserRemoveComponent';
    }

    public function __construct(RequestStack $request, TranslatorInterface $translator)
    {
        parent::__construct($request, $translator);

        $this->formName = USER_REMOVE_FORM_FIELDS::FORM;
        $this->submitFieldName = sprintf('%s[%s]', USER_REMOVE_FORM_FIELDS::FORM, USER_REMOVE_FORM_FIELDS::SUBMIT);
        $this->tokenCsrfFieldName = sprintf('%s[%s]', USER_REMOVE_FORM_FIELDS::FORM, USER_REMOVE_FORM_FIELDS::TOKEN);
    }

    public function mount(UserRemoveComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();
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
                $this->loadErrorsTranslation()
            )
        ->build();
    }

    private function loadErrorsTranslation(): AlertComponentDto
    {
        $errorsLang = [];
        foreach ($this->data->errors as $field => $error) {
            $errorsLang[] = match ($field) {
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
