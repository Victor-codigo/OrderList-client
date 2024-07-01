<?php

declare(strict_types=1);

namespace App\Form\User\EmailChange;

enum EMAIL_CHANGE_FORM_ERRORS: string
{
    case EMAIL = 'email';
    case PASSWORD = 'password';
    case PASSWORD_WRONG = 'password_wrong';
    case TRYOUT_ROUTE_PERMISSIONS = 'tryout_route_permissions';
    case INTERNAL_SERVER = 'internal_server';
}
