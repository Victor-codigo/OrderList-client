<?php

declare(strict_types=1);

namespace App\Form\Order\OrderModify;

use Common\Domain\Form\FormFieldInterface;

final class ORDER_MODIFY_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'order_modify_form';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
    public const ORDER_ID = 'order_id';
    public const DESCRIPTION = 'description';
    public const AMOUNT = 'amount';
    public const PRODUCT_ID = 'product_id';
    public const SHOP_ID = 'shop_id';
    public const LIST_ORDERS_ID = 'list_orders_id';
}
