<?php

declare(strict_types=1);

namespace App\Form\Notification\NotificationRemove;

enum NOTIFICATION_REMOVE_FORM_ERRORS: string
{
    case TRYOUT_ROUTE_PERMISSIONS = 'tryout_route_permissions';
    case INTERNAL_SERVER = 'internal_server';
}
