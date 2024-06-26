<?php

declare(strict_types=1);

namespace App\Form\GroupUsers\GroupUsersAdd;

use Common\Domain\Form\FormErrorInterface;

enum GROUP_USERS_ADD_FORM_ERRORS: string implements FormErrorInterface
{
    case GROUP_ID = 'group_id';
    case USERS = 'users';
    case PERMISSIONS = 'permission';
    case GROUP_NOT_FOUND = 'group_not_found';
    case USERS_VALIDATION = 'users_validation';
    case GROUP_USERS_EXCEEDED = 'group_users_exceeded';
    case GROUP_ALREADY_IN_THE_GROUP = 'group_already_in_the_group';
    case TRYOUT_ROUTE_PERMISSIONS = 'tryout_route_permissions';
    case INTERNAL_SERVER = 'internal';
}
