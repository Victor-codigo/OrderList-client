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
}
