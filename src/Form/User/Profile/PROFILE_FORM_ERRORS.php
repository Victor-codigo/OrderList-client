<?php

declare(strict_types=1);

namespace App\Form\User\Profile;

enum PROFILE_FORM_ERRORS: string
{
    case NAME = 'name';
    case IMAGE = 'image';
    case TRYOUT_ROUTE_PERMISSIONS = 'tryout_route_permissions';
    case INTERNAL_SERVER = 'networkError';
}
