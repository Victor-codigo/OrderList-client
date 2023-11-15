<?php

declare(strict_types=1);

namespace App\Form\Shop\ShopHome;

use Common\Domain\Form\FormFieldInterface;

final class SHOP_HOME_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'list_orders_list_form';
    public const SHOP_REMOVE_MULTIPLE = 'shop_remove_multiple';
    public const TOKEN = 'token';
}
