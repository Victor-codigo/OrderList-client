<?php

declare(strict_types=1);

namespace App\Form\Shop\ShopCreate;

use Common\Domain\Form\FormFieldInterface;

final class SHOP_CREATE_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'shop_create_form';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const IMAGE = 'image';
    public const PRODUCT_ID = 'product_id';
    public const PRODUCT_NAME = 'product_name';
    public const PRODUCT_PRICE = 'product_price';
    public const PRODUCT_UNIT_MEASURE = 'product_unit_measure';
    public const PRODUCT_SEARCH_BUTTON = 'product_search';
}
