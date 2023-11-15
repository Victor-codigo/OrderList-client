<?php

declare(strict_types=1);

namespace App\Form\Shop\ShopList;

use Common\Domain\Form\FormErrorInterface;

enum SHOP_LIST_FORM_ERRORS: string implements FormErrorInterface
{
    case INTERNAL_SERVER = 'error_internal_server';
}
