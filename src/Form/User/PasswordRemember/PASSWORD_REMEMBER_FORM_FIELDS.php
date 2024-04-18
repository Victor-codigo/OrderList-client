<?php

declare(strict_types=1);

namespace App\Form\User\PasswordRemember;

use Common\Domain\Form\FormFieldInterface;

class PASSWORD_REMEMBER_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'password_remember_form';
    public const TOKEN = 'token';
    public const EMAIL = 'email';
    public const SUBMIT = 'sumbit';
}
