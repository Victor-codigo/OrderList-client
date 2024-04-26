<?php

declare(strict_types=1);

namespace App\Form\Group\GroupRemove;

enum GROUP_REMOVE_FORM_ERRORS: string
{
    case GROUP_ID_EMPTY = 'group_id_empty';
    case GROUP_ID = 'group_id';
    case GROUP_NOT_FOUND = 'group_not_found';
    case PERMISSIONS = 'permissions';
    case INTERNAL_SERVER = 'error_internal_server';
}
