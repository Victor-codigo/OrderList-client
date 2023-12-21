<?php

declare(strict_types=1);

namespace App\Form\Product\ProductModify;

use Common\Domain\Form\FormFieldInterface;

final class PRODUCT_MODIFY_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'product_modify_form';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const IMAGE = 'image';
    public const IMAGE_REMOVE = 'image_remove';
}
