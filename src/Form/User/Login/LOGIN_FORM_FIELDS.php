<?php

declare(strict_types=1);

namespace App\Form\User\Login;

use Common\Domain\Form\FormFieldInterface;

final class LOGIN_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'login_form';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';
    public const REMEMBER_ME = 'remember_me';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
    public const CAPTCHA = 'captcha';
}
