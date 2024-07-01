<?php

declare(strict_types=1);

namespace App\Form\User\PasswordChange;

enum PASSWORD_CHANGE_FORM_ERRORS: string
{
    case PASSWORD_OLD = 'passwordOld';
    case PASSWORD_NEW = 'password_new';
    case PASSWORD_NEW_REPEAT = 'password_new_repeat';
    case PASSWORD_CHANGE = 'password_change';
    case PASSWORD_REPEAT = 'password_repeat';
    case TOKEN_WRONG = 'token_wrong';
    case TOKEN_EXPIRED = 'token_expired';
    case TRYOUT_ROUTE_PERMISSIONS = 'tryout_route_permissions';
    case INTERNAL_SERVER = 'internal_server';
}
