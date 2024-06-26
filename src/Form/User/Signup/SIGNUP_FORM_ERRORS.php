<?php

declare(strict_types=1);

namespace App\Form\User\Signup;

enum SIGNUP_FORM_ERRORS: string
{
    case EMAIL = 'email';
    case PASSWORD = 'password';
    case NAME = 'name';
    case EMAIL_EXISTS = 'email_exists';
    case CAPTCHA = 'captcha';
    case INTERNAL_SERVER = 'internal_server';
}
