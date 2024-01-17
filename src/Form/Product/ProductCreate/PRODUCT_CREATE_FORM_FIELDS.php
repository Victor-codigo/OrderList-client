<?php

declare(strict_types=1);

namespace App\Form\Product\ProductCreate;

use Common\Domain\Form\FormFieldInterface;

final class PRODUCT_CREATE_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'product_create_form';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const IMAGE = 'image';
    public const SHOP_ID = 'shop_id';
    public const SHOP_NAME = 'shop_name';
    public const SHOP_PRICE = 'shop_price';
    public const SHOP_SEARCH_BUTTON = 'shop_search';
}
