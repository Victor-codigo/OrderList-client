<?php

declare(strict_types=1);

namespace App\Form\ListOrders\ListOrdersCreate;

use Common\Domain\Form\FormFieldInterface;

final class LIST_ORDERS_CREATE_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'list_orders_create_form';
    public const GROUP_ID = 'group_id';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const DATE_TO_BUY = 'date_to_buy';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
}
