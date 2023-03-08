<?php

declare(strict_types=1);

namespace App\Form\Group\GroupCreate;

use Common\Domain\Form\FormFieldInterface;

final class GROUP_CREATE_FORM_FIELDS implements FormFieldInterface
{
    public const FORM = 'group_create_form';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const IMAGE = 'image';
    public const TOKEN = 'token';
    public const SUBMIT = 'submit';
}
