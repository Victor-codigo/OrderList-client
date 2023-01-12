<?php

declare(strict_types=1);

namespace App\Form\EmailChange;

use Common\Domain\Form\FormFieldInterface;

class EMAIL_CHANGE_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'email_change_form';
    public const TOKEN = 'token';
    public const EMAIL = 'email';
    public const PASSWORD = 'password';
    public const SUBMIT = 'submit';
}
