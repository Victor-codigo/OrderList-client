<?php

declare(strict_types=1);

namespace App\Form\Order\OrderCreate;

use Common\Domain\Form\FormFieldInterface;

final class ORDER_CREATE_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'order_create_form';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
    public const DESCRIPTION = 'description';
    public const AMOUNT = 'amount';
    public const PRODUCT_ID = 'product_id';
    public const SHOP_ID = 'shop_id';
}
