<?php

declare(strict_types=1);

namespace App\Form\User\Login;

use Common\Domain\Form\FormErrorInterface;

enum LOGIN_FORM_ERRORS: string implements FormErrorInterface
{
    case LOGIN = 'error_login';
    case EMAIL = 'email';
    case PASSWORD = 'password';
    case CAPTCHA = 'captcha';
    case INTERNAL_SERVER = 'error_internal_server';
}
