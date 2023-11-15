<?php

declare(strict_types=1);

namespace App\Form\Shop\ShopModify;

use Common\Domain\Form\FormFieldInterface;

final class SHOP_MODIFY_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'product_create_form';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const IMAGE = 'image';
    public const IMAGE_REMOVE = 'image_remove';
}