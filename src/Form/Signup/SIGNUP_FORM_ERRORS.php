<?php

declare(strict_types=1);

namespace App\Form\Signup;

enum SIGNUP_FORM_ERRORS: string
{
    case EMAIL = 'email';
    case PASSWORD = 'password';
    case NAME = 'name';
    case EMAIL_EXISTS = 'email_exists';
    case INTERNAL_SERVER = 'internal_server';
}
