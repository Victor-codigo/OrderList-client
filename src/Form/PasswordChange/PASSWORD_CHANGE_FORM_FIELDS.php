<?php

declare(strict_types=1);

namespace App\Form\PasswordChange;

use Common\Domain\Form\FormFieldInterface;

class PASSWORD_CHANGE_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'password_change_form';
    public const TOKEN = 'token';
    public const USER_ID = 'user_id';
    public const PASSWORD_OLD = 'password_old';
    public const PASSWORD_NEW = 'password_new';
    public const PASSWORD_NEW_REPEAT = 'password_new_repeat';
    public const SUBMIT = 'submit';
}
