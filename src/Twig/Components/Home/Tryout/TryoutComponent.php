<?php

declare(strict_types=1);

namespace App\Twig\Components\Home\Tryout;

use App\Form\User\Login\LOGIN_FORM_FIELDS;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\Config\Config;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'TryoutComponent',
    template: 'Components/Home/Tryout/TryoutComponent.html.twig'
)]
class TryoutComponent extends TwigComponent
{
    private const EMAIL_VALUE = Config::USER_TRY_OUT_EMAIL;
    private const PASSWORD_VALUE = Config::USER_TRY_OUT_PASSWORD;

    public TryoutComponentDto|TwigComponentDtoInterface $data;

    public readonly string $formName;
    public readonly string $tokenCsrfFieldName;
    public readonly string $captchaFieldName;
    public readonly string $submitFieldName;

    public readonly string $emailFieldName;
    public readonly string $emailFieldValue;

    public readonly string $passwordFieldName;
    public readonly string $passwordFieldValue;

    protected static function getComponentName(): string
    {
        return 'TryoutComponent';
    }

    public function mount(TryoutComponentDto $data): void
    {
        $this->data = $data;

        $this->formName = LOGIN_FORM_FIELDS::FORM;
        $this->tokenCsrfFieldName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::TOKEN);
        $this->captchaFieldName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::CAPTCHA);
        $this->emailFieldName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::EMAIL);
        $this->passwordFieldName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::PASSWORD);
        $this->submitFieldName = sprintf('%s[%s]', LOGIN_FORM_FIELDS::FORM, LOGIN_FORM_FIELDS::SUBMIT);

        $this->emailFieldValue = self::EMAIL_VALUE;
        $this->passwordFieldValue = self::PASSWORD_VALUE;
    }
}
