<?php

declare(strict_types=1);

namespace App\Form\Group\GroupCreate;

use Common\Domain\Form\FormErrorInterface;

enum GROUP_CREATE_FORM_ERRORS: string implements FormErrorInterface
{
    case NAME = 'name';
    case DESCRIPTION = 'description';
    case IMAGE = 'image';
    case GROUP_NAME_REPEATED = 'group_name_repeated';
    case INTERNAL_SERVER = 'error_internal_server';
}
