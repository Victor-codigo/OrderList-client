<?php

declare(strict_types=1);

namespace App\Form\User\UserRemove;

enum USER_REMOVE_FORM_ERRORS: string
{
    case TRYOUT_ROUTE_PERMISSIONS = 'tryout_route_permissions';
    case INTERNAL_SERVER = 'internal_server';
}
