<?php

declare(strict_types=1);

namespace App\Form\GroupUsers\GroupUsersRemove;

enum GROUP_USERS_REMOVE_FORM_ERRORS: string
{
    case GROUP_ID_WRONG = 'group_id';
    case USERS = 'users_id';
    case GROUP_WITHOUT_ADMINS = 'group_without_admin';
    case GROUP_EMPTY = 'group_empty';
    case GROUP_USERS_NOT_FOUND = 'group_users_not_found';
    case PERMISSIONS = 'permissions';
    case INTERNAL_SERVER = 'error_internal_server';
}
