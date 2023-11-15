<?php

declare(strict_types=1);

namespace App\Form\Shop\ShopList;

use Common\Domain\Form\FormFieldInterface;

final class SHOP_LIST_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'list_shop_list_form';
    public const SHOP_SELECTED = 'shop_selected';
    public const SHOP_REMOVE = 'shop_remove';
    public const SHOP_REMOVE_MULTIPLE = 'shop_remove_multiple';
    public const TOKEN = 'token';
}
