<?php

declare(strict_types=1);

namespace App\Form\PasswordRemember;

enum PASSWORD_REMEMBER_FORM_ERRORS: string
{
    case EMAIL = 'email';
    case EMAIL_NOT_FOUND = 'email_not_found';
    case INTERNAL_SERVER = 'networkError';
}
