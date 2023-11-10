<?php

declare(strict_types=1);

namespace App\Form\Shop\ShopModify;

use Common\Domain\Form\FormErrorInterface;

enum SHOP_MODIFY_FORM_ERRORS: string implements FormErrorInterface
{
    case SHOP_ID = 'shop_id';
    case GROUP_ID = 'group_id';
    case NAME = 'name';
    case DESCRIPTION = 'description';
    case SHOP_NOT_FOUND = 'shop_not_found';
    case SHOP_NAME_REPEATED = 'shop_name_repeated';
    case IMAGE = 'image';
    case INTERNAL_SERVER = 'error_internal_server';
}
