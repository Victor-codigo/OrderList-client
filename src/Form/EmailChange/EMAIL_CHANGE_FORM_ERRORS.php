<?php

declare(strict_types=1);

namespace App\Form\EmailChange;

enum EMAIL_CHANGE_FORM_ERRORS: string
{
    case EMAIL = 'email';
    case PASSWORD = 'password';
    case PASSWORD_WRONG = 'password_wrong';
    case INTERNAL_SERVER = 'internal_server';
}
