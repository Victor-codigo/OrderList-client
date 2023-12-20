<?php

declare(strict_types=1);

namespace App\Form\Product\ProductModify;

use Common\Domain\Form\FormErrorInterface;

enum PRODUCT_MODIFY_FORM_ERRORS: string implements FormErrorInterface
{
    case GROUP_ID = 'group_id';
    case DESCRIPTION = 'description';
    case GROUP_ERROR = 'group_error';
    case NAME = 'name';
    case PRODUCT_NAME_REPEATED = 'product_name_repeated';
    case IMAGE = 'image';
    case INTERNAL_SERVER = 'error_internal_server';
}
