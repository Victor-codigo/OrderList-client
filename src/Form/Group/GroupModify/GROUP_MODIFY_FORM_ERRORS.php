<?php

declare(strict_types=1);

namespace App\Form\Group\GroupModify;

use Common\Domain\Form\FormErrorInterface;

enum GROUP_MODIFY_FORM_ERRORS: string implements FormErrorInterface
{
    case GROUP_ID = 'group_id';
    case name = 'name';
    case DESCRIPTION = 'description';
    case GROUP_NOT_FOUND = 'group_not_found';
    case IMAGE = 'image';
    case INTERNAL_SERVER = 'error_internal_server';
}
