<?php

declare(strict_types=1);

namespace App\Form\ListOrders\ListOrdersCreateFrom;

use Common\Domain\Form\FormFieldInterface;

final class LIST_ORDERS_CREATE_FROM_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'list_orders_create_form';
    public const LIST_ORDERS_CREATE_FROM_ID = 'list_orders_id_create_from';
    public const NAME = 'name';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
}
