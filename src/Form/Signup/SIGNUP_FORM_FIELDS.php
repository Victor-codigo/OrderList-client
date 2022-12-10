<?php

declare(strict_types=1);

namespace App\Form\Signup;

use Common\Domain\Form\FormFieldInterface;

class SIGNUP_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'signup_form';
    public const TOKEN = 'token';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';
    public const PASSWORD_REPEATED = 'password_repeated';
    public const NICK = 'nick';
    public const SUBMIT = 'sumbit';
}
