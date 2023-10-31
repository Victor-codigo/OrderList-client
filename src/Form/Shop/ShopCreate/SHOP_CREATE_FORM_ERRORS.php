<?php

declare(strict_types=1);

namespace App\Form\Shop\ShopCreate;

use Common\Domain\Form\FormErrorInterface;

enum SHOP_CREATE_FORM_ERRORS: string implements FormErrorInterface
{
    case DESCRIPTION = 'description';
    case GROUP_ID = 'group_id';
    case GROUP_ERROR = 'group_error';
    case NAME = 'name';
    case SHOP_NAME_REPEATED = 'shop_name_repeated';
    case IMAGE = 'image';
    case INTERNAL_SERVER = 'error_internal_server';
}
